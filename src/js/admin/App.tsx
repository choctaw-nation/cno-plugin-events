import { ToggleControl, Button, Spinner, Notice } from '@wordpress/components';
import useOptions from './hooks/useOptions';
import PostTypeSettings from './PostTypeSettings';

export default function App() {
	const { settings, setSettings, saving, notice, save } = useOptions();
	if ( ! settings ) {
		return (
			<div style={ { display: 'flex', alignItems: 'center' } }>
				<h2>Loading...</h2>
				<Spinner />
			</div>
		);
	}

	return (
		<div>
			{ notice && (
				<Notice status={ notice.type }>{ notice.message }</Notice>
			) }
			<div
				className="components-panel"
				style={ { padding: '1rem', marginBlock: '1rem' } }
			>
				<ToggleControl
					__nextHasNoMarginBottom
					label="Enable Post Type"
					checked={ settings.post_type_is_enabled }
					onChange={ ( val ) =>
						setSettings( {
							...settings,
							post_type_is_enabled: !! val,
						} )
					}
				/>
			</div>
			{ settings.post_type_is_enabled && (
				<PostTypeSettings
					settings={ settings }
					setSettings={ setSettings }
				/>
			) }

			<div style={ { marginTop: 16 } }>
				<Button isPrimary isBusy={ saving } onClick={ save }>
					Save Settings
				</Button>
			</div>
		</div>
	);
}
