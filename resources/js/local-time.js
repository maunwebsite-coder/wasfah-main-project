const hasDOM = typeof document !== 'undefined';

if (hasDOM) {
    const localTimeElements = document.querySelectorAll('[data-local-time]');

    if (localTimeElements.length > 0) {
        const resolvedTimezone = (() => {
            try {
                return Intl.DateTimeFormat().resolvedOptions().timeZone;
            } catch (error) {
                console.warn('Unable to detect timezone.', error);
                return null;
            }
        })();

        const FORMAT_MAP = {
            'datetime-full': { dateStyle: 'full', timeStyle: 'short' },
            'datetime-long': { dateStyle: 'long', timeStyle: 'short' },
            'datetime-short': { dateStyle: 'medium', timeStyle: 'short' },
            'date-only': { dateStyle: 'long' },
            'time-only': { timeStyle: 'short' },
        };

        const getLocale = (element) => {
            const attrLocale = element.getAttribute('data-locale');

            if (attrLocale) {
                return attrLocale;
            }

            return document.documentElement.lang || navigator.language || 'en';
        };

        const formatDate = (date, element) => {
            const formatKey = element.getAttribute('data-format') || 'datetime-long';
            const options = FORMAT_MAP[formatKey] || FORMAT_MAP['datetime-long'];
            const locale = getLocale(element);

            try {
                return new Intl.DateTimeFormat(locale, options).format(date);
            } catch (error) {
                console.warn('Failed to format date for locale.', error);
                return date.toLocaleString();
            }
        };

        const formatOffset = (date) => {
            const offsetMinutes = -date.getTimezoneOffset();
            const sign = offsetMinutes >= 0 ? '+' : '-';
            const absoluteMinutes = Math.abs(offsetMinutes);
            const hours = String(Math.floor(absoluteMinutes / 60)).padStart(2, '0');
            const minutes = String(absoluteMinutes % 60).padStart(2, '0');

            return `GMT${sign}${hours}:${minutes}`;
        };

        const applyTemplate = (template, tokens) => {
            if (typeof template !== 'string' || template.trim() === '') {
                template = ':label :date (:offset · :timezone)';
            }

            return template
                .replace(/:([a-z_]+)/gi, (match, key) => {
                    const normalized = key.toLowerCase();
                    return Object.prototype.hasOwnProperty.call(tokens, normalized)
                        ? tokens[normalized]
                        : '';
                })
                .replace(/\s{2,}/g, ' ')
                .trim();
        };

        localTimeElements.forEach((element) => {
            const isoString = element.getAttribute('data-source-time');

            if (!isoString) {
                return;
            }

            const date = new Date(isoString);

            if (Number.isNaN(date.getTime())) {
                return;
            }

            const formattedDate = formatDate(date, element);
            const label = element.getAttribute('data-label') || '';
            const timezoneName = resolvedTimezone
                || element.getAttribute('data-fallback-timezone')
                || '';
            const template = element.getAttribute('data-template');
            const offset = formatOffset(date);
            const tokens = {
                label: label ? `${label}:` : '',
                date: formattedDate,
                timezone: timezoneName,
                offset,
            };

            const textContent = applyTemplate(template, tokens);

            if (textContent) {
                element.textContent = textContent;
                element.setAttribute('data-viewer-timezone', timezoneName);
                element.setAttribute('data-viewer-offset', offset);
            }
        });
    }
}
