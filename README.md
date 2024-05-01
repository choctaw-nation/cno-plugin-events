# cno-events-plugin

A WordPress Plugin for Event Displays.

## Quick Start

1. Download the `choctaw-events-plugin.zip` from the [latest release](https://github.com/choctaw-nation/cno-plugin-events/releases)
2. Install to WordPress

---

# Changelog

## v3.2.5

- Fixed a bug that was incorrectly assigning properties

## v3.2.3

- Now properly handles the new `Requires Plugin` header to require ACF Pro

## v3.2.2

- Init Plugin with WordPress hooks
- Fixed a bug where assigning a custom slug wouldn't override every setting across the plugin.

## v3.2.1

- Fixed a return type bug with `get_the_times` method

## v3.2.0

- Added new `Choctaw_Event` methods for getting venue details without calling the nested `Venue` class (e.g. `$event->venue->the_name()` has been replaced by `$event->the_venue_name()`)
- Added return types to comments.

## v3.1.3

-   Prepped for Github CD
    -   Removed `/dist` to let Github handle
    -   Added `deploy.yml`

## v3.1.2

-   Added return types to all methods of the ACF `class-choctaw-event`
-   Fixed a bug where `Choctaw_Event::the_excerpt` didn't echo properly.

## v3.1.1

-   Fix `DateTime` assignments
-   Fix spelling error in doc comment
-   Update packages

## v3.1.0

-   Adds automatic event expiry with a cron job `expire_choctaw_event_posts`. Handled in the `class-admin-handler` file.

## v3.0.3

-   Update typography to inherit font-family from site.

## v3.0.2

-   Swap `wp_footer` for the correct `get_footer` function calls.

## v3.0.0

-   Inits the new Archive page that handles GraphQL + React Search

### Non-GraphQL Updates

-   Allows a template override for the content parts at `template-parts/events/content-event-preview.php`
-   Updates the Archive query to sort posts by ACF field (instead of publish date).
-   Removes the Search form from the basic field

## v2.2.0

-   Extended Choctaw_Events API
-   New post type field, "Brief Description" (for Yoast & excerpt), in the sidebar of events
-   Bug fixes ([#9](https://github.com/choctaw-nation/cno-plugin-events/issues/9))

## v2.1.0

-   Registers standard archive & single images sizes

## v2.0.5

-   Update AddToCalendar logic to be registered immediately and enqueud in the `single` file.

## v2.0.4

-   Further bug fixes for namespacing.

## v2.0.3

-   Namespace everything and update file names / loaders.

## v2.0.2

-   Handle Output for Venues Content
-   Update ACF JSON field for Venues Fields & Taxonomy

## v2.0.1

-   Total Rework
-   Events without Venues no longer throws JS console errors
