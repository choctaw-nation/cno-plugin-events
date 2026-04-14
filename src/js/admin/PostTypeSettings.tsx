import {
	Panel,
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { Settings } from './types';

interface PostTypeSettingsProps {
	settings: Settings;
	setSettings: ( settings: Settings ) => void;
}
const dFlexColumnStyles: React.CSSProperties = {
	display: 'flex',
	flexDirection: 'column',
	gap: '1rem',
};

export default function PostTypeSettings( {
	settings,
	setSettings,
}: PostTypeSettingsProps ) {
	return (
		<Panel>
			<PanelBody title="Post Settings" initialOpen={ true }>
				<div style={ dFlexColumnStyles }>
					<ToggleControl
						__nextHasNoMarginBottom
						label="Enable ACF Fields"
						checked={ settings.load_acf_fields }
						onChange={ ( val ) =>
							setSettings( {
								...settings,
								load_acf_fields: !! val,
							} )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label="Enable Block Editor"
						checked={ settings.enable_block_editor }
						onChange={ ( val ) =>
							setSettings( {
								...settings,
								enable_block_editor: !! val,
							} )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label="Enable Archive"
						checked={ settings.has_archive }
						onChange={ ( val ) =>
							setSettings( { ...settings, has_archive: !! val } )
						}
					/>
					{ ! settings.enable_block_editor && (
						<p>
							To override display, create a custom template files
							in{ ' ' }
							<code>{ `templates/archive-${ settings.post_type_slug }.php` }</code>{ ' ' }
							and
							<code>{ `templates/single-${ settings.post_type_slug }.php` }</code>
						</p>
					) }
				</div>
			</PanelBody>
			<PanelBody title="Post Type Settings">
				<div style={ dFlexColumnStyles }>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label="Post Type Slug"
						value={ settings.post_type_slug }
						onChange={ ( val ) =>
							setSettings( { ...settings, post_type_slug: val } )
						}
					/>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label="Single Label"
						value={ settings.post_type_label_single }
						onChange={ ( val ) =>
							setSettings( {
								...settings,
								post_type_label_single: val,
							} )
						}
					/>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label="Plural Label"
						value={ settings.post_type_label_plural }
						onChange={ ( val ) =>
							setSettings( {
								...settings,
								post_type_label_plural: val,
							} )
						}
					/>
					{ settings.has_archive && (
						<TextControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label="Archive Slug (optional)"
							value={ settings.archive_slug }
							onChange={ ( val ) =>
								setSettings( {
									...settings,
									archive_slug: val,
								} )
							}
						/>
					) }
				</div>
			</PanelBody>
		</Panel>
	);
}
