# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [3.0.0+] - PHP 8.2+ Era

### V3.0.2 - TBD

- [#36](https://github.com/elie29/validator/issues/36) Compact the CHANGELOG file
- [#35](https://github.com/elie29/validator/issues/35) Minor composer.json update (no breaking changes)

### V3.0.1 - 2025-12-19

- [#33](https://github.com/elie29/validator/issues/33) StringRule concatenation error

### V3.0.0 - 2025-12-12

- Breaking Change: [#31](https://github.com/elie29/validator/issues/31) Upgrade to require PHP 8.2 minimum compatibility

## [2.0.0+] - PHP 7.1+ Era

### V2.0.5 - 2019-12-06

- [#29](https://github.com/elie29/validator/issues/29) CollectionRule enhancement

### V2.0.4 - 2019-12-05

- [#28](https://github.com/elie29/validator/issues/28) CollectionRule to validate collection data
- [#25](https://github.com/elie29/validator/issues/25) PHP 7.4 CI support (code remains PHP 7.1 compliant)

### V2.0.3 - 2019-12-03

- [#26](https://github.com/elie29/validator/issues/26) Changed key type to mixed in all rules

### V2.0.2 - 2019-12-03

- [#23](https://github.com/elie29/validator/issues/23) Made setValue() method public
- [#24](https://github.com/elie29/validator/issues/24) Pass CallableRule instance to callable function

### V2.0.1 - 2019-10-10

- [#22](https://github.com/elie29/validator/issues/22) CallableRule validator

### V2.0.0 - 2019-09-29

- Breaking Change: [#20](https://github.com/elie29/validator/issues/20) Updated composer dependencies

## [1.1.x] - Feature Enhancement Era (2019)

### V1.1.9 - 2019-09-23

- [#19](https://github.com/elie29/validator/issues/19) MultiSelect validator
- [#18](https://github.com/elie29/validator/issues/18) MultipleAndRule returns empty error

### V1.1.8 - 2019-09-16

- [#17](https://github.com/elie29/validator/issues/17) Accept rules for indexed context arrays

### V1.1.7 - 2019-07-11

- [#16](https://github.com/elie29/validator/issues/16) Option to exclude non-existing items from validated context

### V1.1.5 - 2019-03-09

- [#15](https://github.com/elie29/validator/issues/15) Replaced canonize with stringify method

### V1.1.4 - 2019-03-18

- [#14](https://github.com/elie29/validator/issues/14) Rule composition with MultipleAndRule and MultipleOrRule

### V1.1.3 - 2019-02-28

- [#13](https://github.com/elie29/validator/issues/13) BooleanRule now accepts null or empty string

### V1.1.2 - 2019-02-28

- [#12](https://github.com/elie29/validator/issues/12) StringCleanerRule should not clean string on validation error

### V1.1.1 - 2019-02-28

- [#10](https://github.com/elie29/validator/issues/10) StringRule now treats empty array as invalid
- [#11](https://github.com/elie29/validator/issues/11) StringCleanerRule

### V1.1.0 - 2019-02-23

- Breaking Change: [#9](https://github.com/elie29/validator/issues/9) JsonRule now returns decoded value
- [#8](https://github.com/elie29/validator/issues/8) Cast option to return transformed valid values
- [#7](https://github.com/elie29/validator/issues/7) Reorganized constants - each rule manages its own

## [1.0.x] - Foundation Era (2019)

### V1.0.4 - 2019-02-23

- [#6](https://github.com/elie29/validator/issues/6) Support for merged validated data

### V1.0.3 - 2019-02-21

- [#5](https://github.com/elie29/validator/issues/5) Imploded errors method

### V1.0.2 - 2019-02-21

- [#2](https://github.com/elie29/validator/issues/2) Customizable error messages
- [#3](https://github.com/elie29/validator/issues/3) ArrayRule validator
- [#4](https://github.com/elie29/validator/issues/4) Changed options to use constants

### V1.0.1 - 2019-02-21

- [#1](https://github.com/elie29/validator/issues/1) Added rules to Validator constructor signature

### V1.0.0 - 2019-02-20

- Initial release
