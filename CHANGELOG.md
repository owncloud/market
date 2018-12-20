# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [Unreleased]

## [0.3.0] - 2018-12-20

### Changed

- Set max version to 10.1 because core platform is switching to Semver
- Admins can now choose between minor or major update of an app - [#391](https://github.com/owncloud/market/issues/391)
- Bugfix/update dependencies breaking - [#407](https://github.com/owncloud/market/issues/407) [#408](https://github.com/owncloud/market/issues/408)
- Bump jakub-onderka/php-console-highlighter from 0.3.2 to 0.4 - [#392](https://github.com/owncloud/market/issues/392)
- Bump constantinople from 3.0.2 to 3.1.2 - [#412](https://github.com/owncloud/market/issues/412)

### Fixed

- Fix "--all" switch for occ command - [#388](https://github.com/owncloud/market/issues/388)
- Set max node version - [#381](https://github.com/owncloud/market/issues/381)

## [0.2.5] - 2018-07-25

### Fixed

- Rebuild top-left navigation if app was (de)installed - [#359](https://github.com/owncloud/market/issues/359)
- Check license of latest marketplace release - [#362](https://github.com/owncloud/market/pull/362)

## [0.2.4] - 2018-04-17

### Fixed
- Automatic cache invalidation when starting a enterprise trail [#282](https://github.com/owncloud/market/pull/282)
- Prevent update notifications for uninstalled apps [#285](https://github.com/owncloud/market/pull/285)

## [0.2.3] - 2017-11-14
### Fixed

- Show more detailed update information - [#159](https://github.com/owncloud/market/pull/159)
- Darken card box-shadow - [#136](https://github.com/owncloud/market/issues/136)
- Handle cluster setups better - [#125](https://github.com/owncloud/market/issues/125) [#184](https://github.com/owncloud/market/pull/184)

## [0.2.2] - 2017-09-15
### Added

- Added market:uninstall command - [#125](https://github.com/owncloud/market/pull/125)
- Added background job to notify admins about app updates - [#108](https://github.com/owncloud/market/pull/108)

### Changed

- `occ market:list` will return a alphabetical sorted list - [#122](https://github.com/owncloud/market/pull/112)
- `occ market` commands will return non-zero exit codes on failure - [#143](https://github.com/owncloud/market/pull/143)
- Provide more detailed information when marketplace could not be reached - [#141](https://github.com/owncloud/market/pull/141)


### Fixed

- Better handling for cluster setups - [#125](https://github.com/owncloud/market/pull/125)
- Only show enterprise trail button when no license key is set - [#142](https://github.com/owncloud/market/pull/142)
- Top right menu will no longer be condensed - [#149](https://github.com/owncloud/market/pull/149)
- Only show links to publisher pages that are active - [#157](https://github.com/owncloud/market/pull/157)

## [0.2.1] - 2017-07-06

## Added

- Ability to start an enterprise trail from within owncloud - [#107](https://github.com/owncloud/market/issues/107)

## [0.2.0] - 2017-06-30

### Added

- Checking if internet connection is disabled for owncloud - [#91](https://github.com/owncloud/market/pull/91)
- Ability to download bundles - [#89](https://github.com/owncloud/market/pull/89)

### Changed

- If apps are not downloadable, a link to marketplace is provided - [#93](https://github.com/owncloud/market/pull/93)

### Fixed

- Translations have been updated - [#75](https://github.com/owncloud/market/pull/78)
- Erroneous sorting of releases - [#90](https://github.com/owncloud/market/pull/90)

## [0.1.0] - 2017-06-23

### Fixed

- Skip migrations when reinstalling missing code - [#76](https://github.com/owncloud/market/issues/76)
- Reset overwritten core css styles - [#73](https://github.com/owncloud/market/issues/73)

[Unreleased]: https://github.com/owncloud/market/compare/v0.3.0...master
[0.3.0]: https://github.com/owncloud/market/compare/v0.2.5...v0.3.0
[0.2.5]: https://github.com/owncloud/market/compare/v0.2.4...v0.2.5
[0.2.4]: https://github.com/owncloud/market/compare/v0.2.3...v0.2.4
[0.2.3]: https://github.com/owncloud/market/compare/v0.2.2...v0.2.3
[0.2.2]: https://github.com/owncloud/market/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/owncloud/market/compare/v0.2.0...v0.2.1

