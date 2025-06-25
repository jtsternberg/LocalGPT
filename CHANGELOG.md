# Changelog

All notable changes to this project will be documented in this file.

## [1.4.0] - 2024-07-25

### Added

- Integrate with [Models.dev](https://models.dev/) to provide detailed information about AI models, including pricing, context windows, and capabilities.
- A comprehensive test suite using PHPUnit to improve code quality and reliability.
- New `reference` command to easily manage reference files for your GPTs (`--list`, `--add`, `--delete`).
- Add model validation during the `new` command's interactive wizard to ensure selected models exist.
- Extensive examples for each supported provider in the new `examples/` directory.
- Minor additional improvements.

### Changed

- The `models` command is now powered by Models.dev and includes new options:
  - Pass a model ID (e.g., `localgpt models gpt-4o-mini`) to view detailed information.
  - Filter by provider using the `--provider` (`-p`) flag.
  - Get more detailed output with the `--verbose` (`-v`) flag.
- Refactored all commands (`new`, `chat`, `models`, `reference`) to be more robust and testable through dependency injection.
- Improved `README.md` with better getting-started instructions and a dedicated examples section.
- `OllamaProvider` now gracefully handles cases where the Ollama service is not running.

## [1.3.0] - 2024-07-24

### Added

- Support for the Ollama provider, allowing interaction with locally running models.

### Changed

- The `list-models` command dynamically lists models available from a running Ollama instance.

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