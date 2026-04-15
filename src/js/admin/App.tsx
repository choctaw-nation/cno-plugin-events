import { ToggleControl, Button, Spinner, TimePicker, Snackbar } from '@wordpress/components';
import useOptions from './hooks/useOptions';
import PostTypeSettings from './PostTypeSettings';
import { dFlexColumnStyles, TimeConverter } from './utils';

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
			<div
				className="components-panel"
				style={ { padding: '1rem', marginBlock: '1rem' } }
			>
				<h2>Plugin Settings</h2>
				<div style={ dFlexColumnStyles }>
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
					<div>
						<h3>Cron Event Settings</h3>
						<p>Set the time (locally) for the cron event to run that expires past events.</p>
						<TimePicker.TimeInput value={ TimeConverter.stringToTimeValue( settings.cron_time ) } label="Cron event time" is12Hour={ true } onChange={ ( val ) =>
							setSettings( {
								...settings,
								cron_time: TimeConverter.timeValueToString( val ),
							} )
						} />
					</div>
				</div>
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
			{ notice && <div style={ { '--snackbar-position': '3%', position: 'absolute', bottom: 'var(--snackbar-position)', right: 'var(--snackbar-position)' } as React.CSSProperties }>
				<Snackbar explicitDismiss={ true } politeness={ notice.type === 'error' ? 'assertive' : 'polite' }>
					<p>{ notice.message }</p>
				</Snackbar>
			</div> }
		</div>
	);
}
