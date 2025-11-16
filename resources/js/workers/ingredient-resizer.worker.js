import { parseQuantity, formatQuantity } from '../utils/quantity.js';

self.onmessage = (event) => {
	const { requestId, multiplier, ingredients } = event.data || {};

	if (!Array.isArray(ingredients) || !ingredients.length) {
		self.postMessage({ requestId, data: [] });
		return;
	}

	const numericMultiplier = Number(multiplier);
	const safeMultiplier = Number.isFinite(numericMultiplier) ? numericMultiplier : 1;

	const updates = ingredients.map((ingredient) => buildScaledIngredient(ingredient, safeMultiplier));
	self.postMessage({ requestId, data: updates });
};

function buildScaledIngredient(ingredient, multiplier) {
	if (!ingredient || typeof ingredient !== 'object') {
		return { id: null, text: '' };
	}

	const baseText = ingredient.fullText || `${ingredient.quantity || ''} ${ingredient.name || ''}`.trim();
	const { amount, unit } = parseQuantity(ingredient.quantity || '');

	if (amount === null) {
		return { id: ingredient.id, text: baseText };
	}

	const scaledAmount = amount * multiplier;
	const displayQuantity = formatQuantity(scaledAmount);
	const parts = [
		displayQuantity,
		unit,
		ingredient.name,
	].filter((part) => part && part.toString().trim().length > 0);

	return {
		id: ingredient.id,
		text: parts.join(' ').trim() || baseText,
	};
}
