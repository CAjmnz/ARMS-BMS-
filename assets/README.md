# Split A.R.M.S. CSS

The original 2,564-line stylesheet is divided into smaller files without
changing the original rule order. `assets/css/app.css` is the only stylesheet
that the page needs to load.

## Installation

1. Back up the current `assets/css/app.css` in your CodeIgniter project.
2. Copy the included `assets/css/app.css` and `assets/css/modules` folder into
   the project's `assets/css` folder.
3. Keep this single stylesheet link in the shared header view:

```php
<link rel="stylesheet" href="<?php echo base_url('assets/css/app.css'); ?>">
```

4. Remove any additional links that load these module files separately. The
   imports in `app.css` already load them in the correct order.
5. Hard-refresh the browser with `Ctrl + F5` after replacing the files.

## Troubleshooting map

| File | Styles to inspect |
| --- | --- |
| `00-variables.css` | Colors, dimensions, shadows, shared CSS variables |
| `10-dashboard.css` | Welcome banner, summary cards, quick actions, dashboard tables |
| `20-shell-base.css` | Base body, sidebar, topbar, collapsed navigation, avatar |
| `30-components.css` | Buttons, badges, tables, page headers, filters, login, modals, notifications |
| `40-about.css` | About page and technology/system cards |
| `50-shell-modern-overrides.css` | Modern sidebar/topbar shell, main-content position, responsive shell rules |
| `60-sidebar-logo-override.css` | Full, collapsed, and mobile sidebar logo visibility |
| `70-topbar-modern.css` | Current topbar layout, notifications, clock, profile menu, mobile topbar |

## Important

Do not rearrange the imports in `app.css`. The files named `override` and the
modern topbar module intentionally load after the base shell styles so the
current design remains unchanged.
