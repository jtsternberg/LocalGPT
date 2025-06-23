# LocalGPT

A command-line interface for creating and interacting with local, file-based custom GPTs, powered by your favorite AI providers.

This tool is designed to be extensible, allowing you to wrap any AI API. We're launching with support for **Google Gemini**, with more providers coming soon.

## Features

*   **CLI First**: No UI needed. Manage everything from your terminal.
*   **Provider Agnostic**: Designed for extension, starting with Google Gemini.
*   **Local Configuration**: Define your custom GPTs in simple JSON files.
*   **Interactive Chat**: Chat with your custom GPTs directly from the command line.
*   **GPT Builder**: An interactive wizard to help you create your custom GPT configuration files.

## Prerequisites

- PHP 8.1 or higher
- [Composer](https://getcomposer.org/)

## Installation & Configuration

1.  **Install the tool via Composer**:

    ```bash
    composer global require jtsternberg/localgpt
    ```
    *Note: Make sure your global Composer `bin` directory is in your system's `PATH`.*

2.  **Set up your API keys**. Copy the example environment file:
    ```bash
    cp .env.example .env
    ```
3.  **Add your API key** to the new `.env` file. For now, we only need Gemini:
    ```
    # .env
    GEMINI_API_KEY="your_gemini_api_key_here"
    # OPENAI_API_KEY="" # For future use
    # ANTHROPIC_API_KEY="" # For future use
    ```
The CLI will automatically load the required API key based on the `provider` specified in your GPT's configuration file.

## Commands

### Comnmand: `new`
Creates a new GPT configuration via an interactive wizard.

```bash
localgpt new my-first-gpt
```

This will launch a step-by-step wizard that asks you for the following information:
*   **Title**: A name for your GPT.
*   **Description**: A short description of what it does.
*   **Provider**: Select a provider from a list of supported options (e.g., `gemini`).
*   **Model**: Select a model from the chosen provider's available list.
*   **System Prompt**: The core instructions for the GPT. This can be typed directly or pasted into the terminal. The prompt is stored in a `SYSTEM_PROMPT.md` file inside the new GPT's directory.

The wizard will then create a `my-first-gpt/gpt.json` configuration file in your current directory.

### Comnmand: `chat`
Starts an interactive chat session with a specified GPT.

```bash
localgpt chat my-first-gpt
```

### Comnmand: `models`
Lists all available models from the supported AI providers.

```bash
localgpt models
```

## Getting Started with an Example

This repository includes a `pizza-pro` example to help you get started quickly.

### 1. Manual Configuration

You can create a `[name]/gpt.json` file yourself. Review the `pizza-pro/gpt.json` file for an example of the structure.

**`pizza-pro/gpt.json`**

```json
{
    "provider": "gemini",
    "title": "Pizza Pro",
    "description": "Pizza lover",
    "model": "gemini-2.5-flash",
    "system_prompt": "./SYSTEM_PROMPT.md",
    "reference_files": []
}
```

### 2. Chatting with the Example GPT

Once you have configured your API key, you can start a chat session with the `pizza-pro` example.

```bash
localgpt chat pizza-pro
```

This will start an interactive chat session with the pre-configured pizza expert.

**Example Session:**

```
$ localgpt chat pizza-pro
Loading GPT: Pizza Pro
Provider: gemini
Model: gemini-2.5-flash

You can start chatting now. (type 'exit' to quit)

> What is the best pizza in the world?

🤖 The best pizza in the world is the Margherita pizza.
>
