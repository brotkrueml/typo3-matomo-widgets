# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Widget "Annotations" (#21)
- Widget "Create annotation" (#25)

### Changed
- The Matomo Widgets configuration must be stored in the site configuration's config.yaml, imports are not supported anymore (#24, #26)

### Fixed
- Exception thrown by YamlFileLoader in TYPO3 v11.5.0 (#24)

## [1.1.2] - 2021-09-26

### Fixed
- ExtensionManagementUtility::isLoaded() in Services.yaml throws error in TYPO3 v11.4

## [1.1.1] - 2021-09-22

### Fixed
- Undefined logger in YamlFileLoader (#20)

## [1.1.0] - 2021-08-30

### Added
- Option to use configuration of base URL and site ID from EXT:matomo_integration

## [1.0.2] - 2021-08-01

### Fixed
- Consider imports of widget settings into site configuration (#19)

## [1.0.1] - 2021-07-26

### Fixed
- Set fallback for undefined array key in PHP8

## [1.0.0] - 2021-05-04

### Added
- Link from widget to corresponding Matomo report if available (#7)
- Widget "Browser plugins" (#12)
- Widget "Content names" (#8)
- Widget "Content pieces" (#9)
- Widget "Site search keywords" (#14)
- Widget "Site search keywords with no results" (#15)
- Widget for custom dimensions (#6)

### Changed
- Raise minimum required version to TYPO3 10.4.15 and TYPO3 11.2.0
- Use selectCheckBox in site configuration for active widget selection instead of checkboxLabeledToggle, an upgrade wizard is available (#10)

### Fixed
- Correct widget titles with site prefix for "Countries" und "Link to Matomo"

### Removed
- Upgrade wizards for version 0.3 (#11)

## [0.3.2] - 2021-01-11

### Changed
- Raise minimum required version to TYPO3 10.4.11

### Fixed
- Show dashboard widgets in non-composer installation

## [0.3.1] - 2020-12-22

### Added
- Compatibility with TYPO3 v11

## [0.3.0] - 2020-11-27

### Added
- Multi-site capability (#1)
- Parameters for data providers are overridable

### Changed
- Inject background colours into GenericDoughnutChartDataProvider

## [0.2.0] - 2020-07-17

### Added
- Widget "Countries"

### Changed
- Generalise widget configuration

### Fixed
- Correct bar label in bar chart
- Dashboard is usable with an incorrect configuration (#2)

## [0.1.0] - 2020-07-06

Initial release


[Unreleased]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v1.1.2...HEAD
[1.1.2]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.3.2...v1.0.0
[0.3.2]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.3.1...v0.3.2
[0.3.1]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/brotkrueml/typo3-matomo-widgets/releases/tag/v0.1.0
