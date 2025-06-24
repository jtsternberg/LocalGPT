# Changelog

All notable changes to this project will be documented in this file.

## [1.2.0] - 2024-07-24

### Added

- Non-interactive chat mode using `--message` and `--messageFile` flags.
- Support for `reference_files` to provide local file context to GPTs.

### Changed

- Refactored configuration handling with a new `Config` model.
- Improved provider architecture for better extensibility.

## [1.1.0] - 2024-07-24

### Added

- Integrated the `llphant/llphant` library to facilitate multi-provider support.
- Support for Anthropic and OpenAI providers, in addition to Gemini.
- Introduced a `BaseProvider` class to standardize provider implementations.
- Expanded the `ROADMAP.md` file to outline the project's future direction in more detail.
- Updated the `.env.example` file to include API keys for all supported providers.

### Changed

- The `ProviderFactory` was refactored to dynamically create provider instances based on the new architecture.
- The `ChatCommand` was updated to handle the new provider system.
- The `GeminiProvider` was refactored to extend the new `BaseProvider`.

## [1.0.0] - 2024-07-22

Released.

### Added

- Initial release of LocalGPT.
- Support for Google Gemini provider.
- `new` command to create new GPTs.
- `chat` command to interact with GPTs.
- `models` command to list available models.