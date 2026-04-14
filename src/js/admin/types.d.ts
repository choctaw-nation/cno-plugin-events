declare global {
	const cnoEventsAdmin: {
		apiNonce: string;
		apiUrl: string;
		settings: Settings;
	};
}

export type Settings = {
	post_type_is_enabled: boolean;
	load_acf_fields: boolean;
	enable_block_editor: boolean;
	post_type_slug: string;
	post_type_label_single: string;
	post_type_label_plural: string;
	has_archive: boolean;
	archive_slug: string;
};

export {};
