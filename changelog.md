# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [0.4.0] - 2023-10-27
- Feat: Support inbound SMS messages from Front
- Docs: improve the docs and add section about inbound SMS messages 

## [0.3.0] - 2023-10-26
- Feat: Support Laravel v10

## [0.2.2] - 2023-02-17
- Fix: set default `FRONT_FAKE_MESSAGES` config to `false`

## [0.2.1] - 2023-02-17
- Fix: config namespace bug

## [0.2] - 2023-02-16

### Changed Features
- Upgraded PHP-CS-FIXER to v3.4

### New Features
- Fake Messages and write output to the Laravel Log 
  - Set `FRONT_FAKE_MESSAGES=true` in `.env` and inspect the Laravel Log for easier debugging of messages.
- Introduce the changelog
