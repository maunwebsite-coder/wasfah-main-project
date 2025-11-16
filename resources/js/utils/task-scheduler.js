/**
 * Lightweight scheduler helpers to keep heavy DOM/JS work off the main thread.
 */

const hasWindow = typeof window !== 'undefined';
const fallbackRaf = hasWindow && typeof window.requestAnimationFrame === 'function'
	? (cb) => window.requestAnimationFrame(cb)
	: (cb) => setTimeout(() => cb(Date.now()), 16);

/**
 * Queue a microtask with a Promise fallback for older browsers.
 * @param {Function} callback
 */
export function queueMicroTask(callback) {
	if (typeof callback !== 'function') {
		return;
	}

	if (typeof queueMicrotask === 'function') {
		queueMicrotask(callback);
	} else {
		Promise.resolve().then(callback);
	}
}

/**
 * Run a callback on the next animation frame (or timeout fallback).
 * @param {Function} callback
 */
export function runOnNextFrame(callback) {
	if (typeof callback !== 'function') {
		return;
	}

	fallbackRaf(() => callback());
}

/**
 * Schedule work during the browser's idle periods.
 * @param {Function} callback
 * @param {{timeout?: number}} options
 * @returns {{id: number, type: 'idle'|'timeout'}}
 */
export function scheduleIdleTask(callback, options = {}) {
	const timeout = Number.isFinite(options.timeout) ? options.timeout : 500;

	if (!hasWindow || typeof window.requestIdleCallback !== 'function') {
		const id = setTimeout(() => callback({
			didTimeout: false,
			timeRemaining: () => 0,
		}), timeout);
		return { id, type: 'timeout' };
	}

	const id = window.requestIdleCallback(callback, { timeout });
	return { id, type: 'idle' };
}

/**
 * Cancel an idle task previously created by scheduleIdleTask.
 * @param {{id: number, type: 'idle'|'timeout'}|null|undefined} handle
 */
export function cancelIdleTask(handle) {
	if (!handle) {
		return;
	}

	if (handle.type === 'idle' && hasWindow && typeof window.cancelIdleCallback === 'function') {
		window.cancelIdleCallback(handle.id);
	} else {
		clearTimeout(handle.id);
	}
}

/**
 * Process a large array in smaller chunks, yielding between batches so the UI stays responsive.
 * @template T
 * @param {T[]} items
 * @param {(item: T, index: number) => void} worker
 * @param {{chunkSize?: number, priority?: 'idle'|'animation'|'microtask', onChunkEnd?: (ctx: {start: number, end: number, done: boolean}) => void}} options
 * @returns {Promise<void>}
 */
export function processInChunks(items, worker, options = {}) {
	const chunkSize = Number.isFinite(options.chunkSize) && options.chunkSize > 0 ? options.chunkSize : 10;
	const priority = options.priority || 'idle';
	const onChunkEnd = typeof options.onChunkEnd === 'function' ? options.onChunkEnd : null;

	if (!Array.isArray(items) || items.length === 0 || typeof worker !== 'function') {
		if (onChunkEnd) {
			onChunkEnd({ start: 0, end: 0, done: true });
		}
		return Promise.resolve();
	}

	let index = 0;

	return new Promise((resolve) => {
		const runChunk = () => {
			const start = index;
			const end = Math.min(start + chunkSize, items.length);

			for (let i = start; i < end; i += 1) {
				worker(items[i], i);
			}

			if (onChunkEnd) {
				onChunkEnd({ start, end, done: end >= items.length });
			}

			index = end;
			if (index < items.length) {
				scheduleNextTick(runChunk, priority);
			} else {
				resolve();
			}
		};

		scheduleNextTick(runChunk, priority);
	});
}

function scheduleNextTick(fn, priority) {
	if (!hasWindow) {
		setTimeout(fn, 0);
		return;
	}

	switch (priority) {
		case 'animation':
			fallbackRaf(() => fn());
			break;
		case 'microtask':
			queueMicroTask(fn);
			break;
		default:
			scheduleIdleTask(() => fn());
			break;
	}
}
