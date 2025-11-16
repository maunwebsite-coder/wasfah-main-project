/**
 * Normalize Arabic numerals so they can be parsed as numbers.
 * @param {string} value
 * @returns {string}
 */
export function normalizeNumerals(value) {
	if (!value || typeof value !== 'string') {
		return '';
	}

	const map = {
		'٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4',
		'٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9',
		'۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
		'۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9',
	};

	return value.replace(/[٠-٩۰-۹]/g, (digit) => map[digit] ?? digit);
}

/**
 * Parse a quantity string that may include fractions (e.g. "1 1/2").
 * @param {string} value
 * @returns {{amount: number|null, unit: string}}
 */
export function parseQuantity(value) {
	const result = { amount: null, unit: '' };

	if (!value || typeof value !== 'string') {
		return result;
	}

	const normalized = normalizeNumerals(value).replace(/،/g, ',').trim();
	if (!normalized) {
		return result;
	}

	let match = normalized.match(/^(-?\d+)\s+(\d+)\/(\d+)\s*(.*)$/);
	if (match) {
		const whole = parseFloat(match[1]);
		const numerator = parseFloat(match[2]);
		const denominator = parseFloat(match[3]);
		const unit = match[4]?.trim() ?? '';

		if (!Number.isNaN(whole) && !Number.isNaN(numerator) && !Number.isNaN(denominator) && denominator !== 0) {
			result.amount = whole + (numerator / denominator);
			result.unit = unit;
			return result;
		}
	}

	match = normalized.match(/^(\d+)\/(\d+)\s*(.*)$/);
	if (match) {
		const numerator = parseFloat(match[1]);
		const denominator = parseFloat(match[2]);
		const unit = match[3]?.trim() ?? '';

		if (!Number.isNaN(numerator) && !Number.isNaN(denominator) && denominator !== 0) {
			result.amount = numerator / denominator;
			result.unit = unit;
			return result;
		}
	}

	match = normalized.match(/^(-?\d+(?:[.,]\d+)?)\s*(.*)$/);
	if (match) {
		const numericPart = parseFloat(match[1].replace(',', '.'));
		if (!Number.isNaN(numericPart)) {
			result.amount = numericPart;
			result.unit = match[2]?.trim() ?? '';
			return result;
		}
	}

	return result;
}

/**
 * Format a numeric value into a concise string (supports common fractions).
 * @param {number|null} value
 * @returns {string}
 */
export function formatQuantity(value) {
	if (value === null || Number.isNaN(value)) {
		return '';
	}

	const rounded = Math.round(value * 100) / 100;

	if (Math.abs(rounded - 0.25) < 0.001) return '1/4';
	if (Math.abs(rounded - 0.5) < 0.001) return '1/2';
	if (Math.abs(rounded - 0.75) < 0.001) return '3/4';

	if (Number.isInteger(rounded)) {
		return rounded.toString();
	}

	return rounded.toString().replace('.', '.');
}
