export const fuzzySearchKeys = {
	keys: [
		{ name: 'title', weight: 1 },
		{ name: 'event_info.description', weight: 0.5 },
		{ name: 'event_info.info.day', weight: 0.03 },
		{ name: 'type.name', weight: 0.8 },
		{ name: 'locations.name', weight: 0.8 },
	],
};
