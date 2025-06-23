# LocalGPT - TODO

This document outlines the development tasks for the `localgpt` CLI tool. It will use the `symfony/console` component for its structure, inspired by the provided helper scripts.

## Phase 1: Project Setup & Configuration

- [x] **Project Setup**
    - [x] Initialize a Composer project: `composer init`.
    - [x] Edit `composer.json`: set name to `jtsternberg/localgpt`, define PSR-4 autoloading for `src/`, and set the `bin` directory.
    - [x] Add dependencies: `composer require symfony/console vlucas/phpdotenv`.
    - [x] Create the main executable `bin/localgpt`. This file will instantiate a `Symfony\Component\Console\Application`, register the commands, and run it.
    - [x] Create a standard `.gitignore` file for a Composer project.
    - [x] Manually create the `.env.example` file with `GEMINI_API_KEY=""`.

- [x] **Create `sample.gpt.json` file**
    - [x] Create a `sample.gpt.json` file to serve as the structural reference for GPT configurations.

- [x] **Configuration Service**
    - [x] Create a `Config` service class in `src/Service/` to handle locating and parsing `gpt.json` files by directory name. This service will be used by both `new` and `chat` commands.

## Phase 2: `new` Command (GPT Builder)

- [x] **`new` Command (Symfony Edition)**
    - [x] Create `src/Command/NewCommand.php` that extends `Symfony\Component\Console\Command\Command`.
    - [x] Configure the command name (`new`) and define an argument for the new GPT name.
    - [x] Use `SymfonyStyle` to create an interactive wizard, prompting for Title, Description, Provider, etc.
    - [x] Create the GPT directory (e.g., `my-new-gpt/`).
    - [x] Create the `reference-files/` subdirectory.
    - [x] Save the system prompt to `SYSTEM_PROMPT.md`.
    - [x] Copy any provided reference files into the `reference-files/` directory.
    - [x] Generate the final `gpt.json` file.

## Phase 3: `chat` Command & Provider Integration

- [x] **Provider System**
    - [x] Create a service in `src/Service/` to load the correct API key from the `.env` file based on the provider.
    - [x] Define a `ProviderInterface` in `src/Provider/` with a `chat(array $messages): string` method.
    - [x] Create a `GeminiProvider` class in `src/Provider/` that implements the interface. It will use an HTTP client to connect to the Gemini API.

- [x] **`chat` Command (Symfony Edition)**
    - [x] Create `src/Command/ChatCommand.php` that extends `Symfony\Component\Console\Command\Command`.
    - [x] Configure the command name (`chat`) and define an argument for the GPT name.
    - [x] In the `execute` method, use the `Config` service to load the specified GPT configuration.
    - [x] Read the system prompt and reference files.
    - [x] Use the `QuestionHelper` to create an interactive input loop.
    - [x] On each input, call the `GeminiProvider` with the full context (system prompt, history, user message).
    - [x] Use the `OutputInterface` to print the provider's response.
    - [x] Implement an `exit` keyword to terminate the chat.

## Phase 4: Finalization & Documentation

- [x] Create a complete `pizza-pro` example directory with its `gpt.json` and `SYSTEM_PROMPT.md`.
- [x] Review and finalize `README.md` to ensure all commands and features are accurately documented. (and explain how to use the `pizza-pro` example for testing)
- [X] Prepare `composer.json` for submission to Packagist.
- [ ] Create a GitHub release.