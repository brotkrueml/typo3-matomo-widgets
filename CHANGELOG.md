# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Link from widget to corresponding Matomo report if available (#7)
- Widget "Browser Plugins" (#12)
- Widget for Custom Dimensions (#6)

### Changed
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


[Unreleased]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.3.2...HEAD
[0.3.2]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.3.1...v0.3.2
[0.3.1]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/brotkrueml/typo3-matomo-widgets/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/brotkrueml/typo3-matomo-widgets/releases/tag/v0.1.0
