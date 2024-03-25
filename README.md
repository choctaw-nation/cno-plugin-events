# cno-events-plugin

A WordPress Plugin for Event Displays.

# Changelog

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
