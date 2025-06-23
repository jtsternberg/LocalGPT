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

## Usage

There are three main commands: `localgpt new`, `localgpt chat`, and `localgpt models`.

### 1. Creating a Custom GPT

To create a new GPT, you can either create a JSON file manually or use the interactive builder.
Review [sample.gpt.json](sample.gpt.json) for an example.

#### Using the Interactive Builder

Run the `new` command:

```bash
localgpt new my-first-gpt
```

This will launch a step-by-step wizard that asks you for the following information:
*   **Title**: A name for your GPT.
*   **Description**: A short description of what it does.
*   **Provider**: The AI provider to use (e.g., `gemini`).
*   **Model**: The specific model for that provider (e.g., `gemini-1.5-pro-latest`).
*   **System Prompt**: The core instructions for the GPT. This should be markdown, or a path to a markdown file. The final prompt is stored to a `SYSTEM_PROMPT.md` file in a `my-first-gpt` directory, and the file path is saved in the GPT configuration file.
*   **Reference Files**: Paths to local files to be included as context. These are stored in a `reference-files` directory in the `my-first-gpt` directory, and the file paths are saved in the GPT configuration file.

The wizard will then create a `my-first-gpt/gpt.json` configuration file in your current directory.

#### Manual Configuration

You can also create a `[name]/gpt.json` file yourself. Review [sample.gpt.json](sample.gpt.json) for an example.

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

### 2. Chatting with a Custom GPT

Once you have your GPT configuration file, you can start a chat session with it using the `chat` command.

```bash
localgpt chat pizza-pro
```

This will start an interactive chat session. The CLI will read the JSON file, load the correct provider (Gemini, in this case), and use the system prompt, reference files, and model to have a contextual conversation.

**Example Session:**

```
$ localgpt chat pizza-pro
Loading GPT: Pizza Pro...
Provider: gemini
Model: gemini-2.5-flash
You can start chatting now. (type 'exit' to quit)

> What is the best pizza in the world?

🤖 The best pizza in the world is the Margherita pizza.
>

### 3. Listing Available Models

To see a list of available models from the supported providers, use the `models` command:

```bash
localgpt models
```

This will output a list of models grouped by provider.
