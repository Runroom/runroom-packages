# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

<!-- changelog-linker -->

## 0.9.2 - 2020-11-16

### RunroomRenderEventBundle

#### Fixed

- [#79] Fix ErrorRenderer

## [0.9.1] - 2020-10-29

### All

#### Changed

- [#77] Replace prophecy for phpunit mocks
- [#76] Composer v2 and php 8 support

### RunroomCookiesBundle

#### Removed

- [#75] Remove csrf from CookiesFormType

## [0.9.0] - 2020-09-29

### RunroomCookiesBundle

#### Fixed

- [#74] Disable performance cookies by default

### All

#### Added

- [#68] Add shepherd type check

#### Changed

- [#70] Transform config to PHP and avoid using yaml for internal config
- [#66] Increase psalm to level 4, with baseline

#### Fixed

- [#73] Fix phpstan issues

#### Removed

- [#72] Remove verbose logs
- [#69] Remove SonataEasyExtendsBundle

## [v0.8.2] - 2020-07-08

### RunroomCookiesBundle

#### Fixed

- [#65] Fix CookiesPageService arguments

## [v0.8.1] - 2020-07-08

### Testing

#### Added

- [#64] Add assert for show fields

## [v0.8.0] - 2020-07-08

### RunroomCookiesBundle

#### Added

- [#29] Add bundle, Thanks to [@DaniCristante]

### RunroomSeoBundle

- [#48] Add real placeholders using property-access

### Testing

- [#62] Add AdminTestCase

### All Packages

- [#63] add missing test
- [#60] add coverage for missing repositories
- [#55] Add split ci testing

#### Changed

- [#61] improve coverage 2.0
- [#57] PHPStan level 8
- [#54] Improve code quality
- [#53] Implement automatic redirections based on entity modifications
- [#50] Improve static code analisis
- [#49] Simplify integration test and upgrade packages

#### Fixed

- [#52] Fix entity repository
- [#51] Fix nullable types

#### Removed

- [#58] Remove const whenever possible

## [v0.7.1] - 2020-06-03

### RunroomSortableBehaviorBundle

#### Fixed

- [#47] Fix service in SortableAdminController

## [v0.7.0] - 2020-06-02

### RunroomRedirectionBundle

#### Changed

- [#33] Use ServiceEntityRepository

### All packages

#### Added

- [#28] Add coverage
- [#30] Add badge to readme
- [#34] Add psalm, allow persistence 2.0 and common 3.0

#### Changed

- [#37] Test with lowest dependencies
- [#35] Improve code quality
- [#36] Keep improving code quality
- [#46] Increase coverage of SortableBehaviorBundle
- [#45] Increase coverage on ORMPositionHandler
- [#40] More tests
- [#41] Increase coverage on RenderEventBundle and RedirectionBundle
- [#42] Increase sortable behavior coverage
- [#43] Improve admin services
- [#44] Improve coverage
- [#38] Initial Unit Test for GedmoPositionHandler

#### Fixed

- [#39] Improve coverage and fix deprecation
- [#31] Increase coverage and minor fixes to increase code quality

## [v0.6.9] - 2020-05-27

### RunroomBasicPageBundle

#### Fixed

- [#27] Fix Sonata open button

## [v0.6.8] - 2020-05-27

### RunroomBasicPageBundle

#### Added

- [#26] Make controll public and add missing tag

## [v0.6.7] - 2020-05-27

### RunroomBasicPageBundle

#### Added

- [#25] Replace static with basic, add missing tag

## [v0.6.6] - 2020-05-27

### RunroomBasicPageBundle

#### Changed

- [#24] Change route name

## [v0.6.5] - 2020-05-26

### RunroomBasicPageBundle

#### Fixed

- [#23] Fix template vars

## [v0.6.4] - 2020-05-26

### RunroomBasicPageBundle

#### Added

- [#22] Add show template

## [v0.6.3] - 2020-05-26

### RunroomBasicPageBundle

#### Added

- [#21] Add basic page bundle

### RunroomRedirectionBundle

#### Changed

- [#19] Update test case

### RunroomSeoBundle

- [#20] Allow configure media entity

## [v0.6.2] - 2020-05-25

### RunroomRenderEventBundle

#### Added

- [#18] Add Render Error

## [v0.6.1] - 2020-05-25

### RunroomSeoBundle

#### Fixed

- [#17] Fix Doctrine repository tag

### All packages

#### Added

- [#16] Add purpose and credits on READMEs

## [v0.6.0] - 2020-05-22

### RunroomSeoBundle

#### Added

- [#15] Add configuration and mark final
- [#14] Add Bundle

## [v0.5.0] - 2020-05-13

### RunroomCkeditorSonataMediaBundle

#### Added

- [#13] Add package

## [v0.4.1] - 2020-05-13

### RunroomTranslationBundle

#### Added

- [#12] Add compatibility with a2lix 3.0

### All packages

#### Changed

- [#9] Update to phpunit 9

## [v0.4.0] - 2020-05-07

### FormHandlerBundle

#### Added

- [#11] Refactor on Form Handler: Add options and remove set initial data

## [v0.3.1] - 2020-04-22

### RenderEventBundle

#### Added

- [#10] Add context parameter

## [0.3.0] - 2020-03-19

### FormHandlerBundle

#### Added

- [#8] Add Initial commit for FormHandlerBundle

## [0.2.1] - 2020-03-18

### SortableBehaviorBundle

#### Added

- [#6] Add AbstractSortableAdmin and Sortable Trait

## [0.2.0] - 2020-03-16

### RenderEventBundle

#### Changed

- [#5] Rename RenderEventBundle to RunroomRenderEventBundle

## [0.1.1] - 2020-03-16

### RunroomPackages

#### Fixed

- [#4] Fix release process, normalize before release

## [0.1.0] - 2020-03-16

### RunroomPackages

#### Added

- [#1] Add Initial configuration

#### Changed

- [#2] Update readme

#### Fixed

- [#3] Fix composer.json for all subpackages

[#1]: https://github.com/Runroom/runroom-packages/pull/1
[#2]: https://github.com/Runroom/runroom-packages/pull/2
[#3]: https://github.com/Runroom/runroom-packages/pull/3
[#4]: https://github.com/Runroom/runroom-packages/pull/4
[#5]: https://github.com/Runroom/runroom-packages/pull/5
[0.1.1]: https://github.com/Runroom/runroom-packages/compare/0.1.0...0.1.1
[0.2.0]: https://github.com/Runroom/runroom-packages/compare/0.1.1...0.2.0
[#6]: https://github.com/Runroom/runroom-packages/pull/6
[#8]: https://github.com/Runroom/runroom-packages/pull/8
[0.2.1]: https://github.com/Runroom/runroom-packages/compare/0.2.0...0.2.1
[0.3.0]: https://github.com/Runroom/runroom-packages/compare/0.2.1...0.3.0
[#10]: https://github.com/Runroom/runroom-packages/pull/10
[#11]: https://github.com/Runroom/runroom-packages/pull/11
[v0.3.1]: https://github.com/Runroom/runroom-packages/compare/0.3.0...v0.3.1
[#12]: https://github.com/Runroom/runroom-packages/pull/12
[#9]: https://github.com/Runroom/runroom-packages/pull/9
[v0.4.0]: https://github.com/Runroom/runroom-packages/compare/v0.3.1...v0.4.0
[#13]: https://github.com/Runroom/runroom-packages/pull/13
[v0.4.1]: https://github.com/Runroom/runroom-packages/compare/v0.4.0...v0.4.1
[#15]: https://github.com/Runroom/runroom-packages/pull/15
[#14]: https://github.com/Runroom/runroom-packages/pull/14
[v0.5.0]: https://github.com/Runroom/runroom-packages/compare/v0.4.1...v0.5.0
[#17]: https://github.com/Runroom/runroom-packages/pull/17
[#16]: https://github.com/Runroom/runroom-packages/pull/16
[v0.6.0]: https://github.com/Runroom/runroom-packages/compare/v0.5.0...v0.6.0
[#18]: https://github.com/Runroom/runroom-packages/pull/18
[v0.6.1]: https://github.com/Runroom/runroom-packages/compare/v0.6.0...v0.6.1
[#21]: https://github.com/Runroom/runroom-packages/pull/21
[#20]: https://github.com/Runroom/runroom-packages/pull/20
[#19]: https://github.com/Runroom/runroom-packages/pull/19
[v0.6.2]: https://github.com/Runroom/runroom-packages/compare/v0.6.1...v0.6.2
[#22]: https://github.com/Runroom/runroom-packages/pull/22
[v0.6.3]: https://github.com/Runroom/runroom-packages/compare/v0.6.2...v0.6.3
[#23]: https://github.com/Runroom/runroom-packages/pull/23
[v0.6.4]: https://github.com/Runroom/runroom-packages/compare/v0.6.3...v0.6.4
[#24]: https://github.com/Runroom/runroom-packages/pull/24
[v0.6.5]: https://github.com/Runroom/runroom-packages/compare/v0.6.4...v0.6.5
[#25]: https://github.com/Runroom/runroom-packages/pull/25
[v0.6.6]: https://github.com/Runroom/runroom-packages/compare/v0.6.5...v0.6.6
[#26]: https://github.com/Runroom/runroom-packages/pull/26
[v0.6.7]: https://github.com/Runroom/runroom-packages/compare/v0.6.6...v0.6.7
[#27]: https://github.com/Runroom/runroom-packages/pull/27
[v0.6.8]: https://github.com/Runroom/runroom-packages/compare/v0.6.7...v0.6.8
[#46]: https://github.com/Runroom/runroom-packages/pull/46
[#45]: https://github.com/Runroom/runroom-packages/pull/45
[#44]: https://github.com/Runroom/runroom-packages/pull/44
[#43]: https://github.com/Runroom/runroom-packages/pull/43
[#42]: https://github.com/Runroom/runroom-packages/pull/42
[#41]: https://github.com/Runroom/runroom-packages/pull/41
[#40]: https://github.com/Runroom/runroom-packages/pull/40
[#39]: https://github.com/Runroom/runroom-packages/pull/39
[#38]: https://github.com/Runroom/runroom-packages/pull/38
[#37]: https://github.com/Runroom/runroom-packages/pull/37
[#36]: https://github.com/Runroom/runroom-packages/pull/36
[#35]: https://github.com/Runroom/runroom-packages/pull/35
[#34]: https://github.com/Runroom/runroom-packages/pull/34
[#33]: https://github.com/Runroom/runroom-packages/pull/33
[#31]: https://github.com/Runroom/runroom-packages/pull/31
[#30]: https://github.com/Runroom/runroom-packages/pull/30
[#28]: https://github.com/Runroom/runroom-packages/pull/28
[v0.6.9]: https://github.com/Runroom/runroom-packages/compare/v0.6.8...v0.6.9
[#47]: https://github.com/Runroom/runroom-packages/pull/47
[v0.7.0]: https://github.com/Runroom/runroom-packages/compare/v0.6.9...v0.7.0
[#63]: https://github.com/Runroom/runroom-packages/pull/63
[#62]: https://github.com/Runroom/runroom-packages/pull/62
[#61]: https://github.com/Runroom/runroom-packages/pull/61
[#60]: https://github.com/Runroom/runroom-packages/pull/60
[#58]: https://github.com/Runroom/runroom-packages/pull/58
[#57]: https://github.com/Runroom/runroom-packages/pull/57
[#55]: https://github.com/Runroom/runroom-packages/pull/55
[#54]: https://github.com/Runroom/runroom-packages/pull/54
[#53]: https://github.com/Runroom/runroom-packages/pull/53
[#52]: https://github.com/Runroom/runroom-packages/pull/52
[#51]: https://github.com/Runroom/runroom-packages/pull/51
[#50]: https://github.com/Runroom/runroom-packages/pull/50
[#49]: https://github.com/Runroom/runroom-packages/pull/49
[#48]: https://github.com/Runroom/runroom-packages/pull/48
[#29]: https://github.com/Runroom/runroom-packages/pull/29
[v0.7.1]: https://github.com/Runroom/runroom-packages/compare/v0.7.0...v0.7.1
[@DaniCristante]: https://github.com/DaniCristante
[#64]: https://github.com/Runroom/runroom-packages/pull/64
[v0.8.0]: https://github.com/Runroom/runroom-packages/compare/v0.7.1...v0.8.0
[#65]: https://github.com/Runroom/runroom-packages/pull/65
[v0.8.1]: https://github.com/Runroom/runroom-packages/compare/v0.8.0...v0.8.1
[#74]: https://github.com/Runroom/runroom-packages/pull/74
[#73]: https://github.com/Runroom/runroom-packages/pull/73
[#72]: https://github.com/Runroom/runroom-packages/pull/72
[#70]: https://github.com/Runroom/runroom-packages/pull/70
[#69]: https://github.com/Runroom/runroom-packages/pull/69
[#68]: https://github.com/Runroom/runroom-packages/pull/68
[#66]: https://github.com/Runroom/runroom-packages/pull/66
[v0.8.2]: https://github.com/Runroom/runroom-packages/compare/v0.8.1...v0.8.2
[#77]: https://github.com/Runroom/runroom-packages/pull/77
[#76]: https://github.com/Runroom/runroom-packages/pull/76
[#75]: https://github.com/Runroom/runroom-packages/pull/75
[0.9.0]: https://github.com/Runroom/runroom-packages/compare/v0.8.2...0.9.0
[#79]: https://github.com/Runroom/runroom-packages/pull/79
[0.9.1]: https://github.com/Runroom/runroom-packages/compare/0.9.0...0.9.1
