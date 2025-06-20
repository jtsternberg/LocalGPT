# LocalGPT - TODO

This document outlines the development tasks for the `localgpt` CLI tool. It will use the `symfony/console` component for its structure, inspired by the provided helper scripts.

## Phase 1: Project Setup & Configuration

- [ ] **Project Setup**
    - [ ] Initialize a Composer project: `composer init`.
    - [ ] Edit `composer.json`: set name to `jtsternberg/localgpt`, define PSR-4 autoloading for `src/`, and set the `bin` directory.
    - [ ] Add dependencies: `composer require symfony/console vlucas/phpdotenv`.
    - [ ] Create the main executable `bin/localgpt`. This file will instantiate a `Symfony\Component\Console\Application`, register the commands, and run it.
    - [ ] Create a standard `.gitignore` file for a Composer project.
    - [ ] Manually create the `.env.example` file with `GEMINI_API_KEY=""`.

- [ ] **Create `sample.gpt.json` file**
    - [ ] Create a `sample.gpt.json` file to serve as the structural reference for GPT configurations.

- [ ] **Configuration Service**
    - [ ] Create a `Config` service class in `src/Service/` to handle locating and parsing `gpt.json` files by directory name. This service will be used by both `new` and `chat` commands.

## Phase 2: `new` Command (GPT Builder)

- [ ] **`new` Command (Symfony Edition)**
    - [ ] Create `src/Command/NewCommand.php` that extends `Symfony\Component\Console\Command\Command`.
    - [ ] Configure the command name (`new`) and define an argument for the new GPT name.
    - [ ] Use the `QuestionHelper` to create an interactive wizard, prompting for Title, Description, Provider, etc.
    - [ ] Create the GPT directory (e.g., `my-new-gpt/`).
    - [ ] Create the `reference-files/` subdirectory.
    - [ ] Save the system prompt to `SYSTEM_PROMPT.md`.
    - [ ] Copy any provided reference files into the `reference-files/` directory.
    - [ ] Generate the final `gpt.json` file.

## Phase 3: `chat` Command & Provider Integration

- [ ] **Provider System**
    - [ ] Create a service in `src/Service/` to load the correct API key from the `.env` file based on the provider.
    - [ ] Define a `ProviderInterface` in `src/Provider/` with a `chat(array $messages): string` method.
    - [ ] Create a `GeminiProvider` class in `src/Provider/` that implements the interface. It will use an HTTP client to connect to the Gemini API.

- [ ] **`chat` Command (Symfony Edition)**
    - [ ] Create `src/Command/ChatCommand.php` that extends `Symfony\Component\Console\Command\Command`.
    - [ ] Configure the command name (`chat`) and define an argument for the GPT name.
    - [ ] In the `execute` method, use the `Config` service to load the specified GPT configuration.
    - [ ] Read the system prompt and reference files.
    - [ ] Use the `QuestionHelper` to create an interactive input loop.
    - [ ] On each input, call the `GeminiProvider` with the full context (system prompt, history, user message).
    - [ ] Use the `OutputInterface` to print the provider's response.
    - [ ] Implement an `exit` keyword to terminate the chat.

## Phase 4: Finalization & Documentation

- [ ] Create a complete `spanish-english-translator` example directory with its `gpt.json` and `SYSTEM_PROMPT.md`.
- [ ] Review and finalize `README.md` to ensure all commands and features are accurately documented. (and explain how to use the `spanish-english-translator` example for testing)
- [ ] Prepare `composer.json` for submission to Packagist.
- [ ] Create a GitHub release.
