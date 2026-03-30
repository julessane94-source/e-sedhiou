import './bootstrap';

const applyDynamicWidths = () => {
	document.querySelectorAll('[data-width]').forEach((element) => {
		const rawValue = Number(element.getAttribute('data-width'));
		const safeValue = Number.isFinite(rawValue) ? Math.max(0, Math.min(rawValue, 100)) : 0;

		element.style.width = `${safeValue}%`;
	});
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', applyDynamicWidths, { once: true });
} else {
	applyDynamicWidths();
}
