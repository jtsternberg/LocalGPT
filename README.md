# LocalGPT

![LocalGPT](https://github.com/user-attachments/assets/f297580a-0de2-4443-b932-1e8fc85e4432)

A command-line interface for creating and interacting with local, file-based custom GPTs, powered by your favorite AI providers.

This tool is designed to be extensible, allowing you to wrap any AI API. We're launching with support for **Google Gemini**, **OpenAI**, and **Anthropic**, with more providers coming soon.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation & Configuration](#installation--configuration)
- [Commands](#commands)
  - [Command: `new`](#command-new)
  - [Command: `chat`](#command-chat)
  - [Command: `models`](#command-models)
- [Getting Started with an Example](#getting-started-with-an-example)
  - [1. Manual Configuration](#1-manual-configuration)
  - [2. Chatting with the Example GPT](#2-chatting-with-the-example-gpt)
- [Additional Features](#additional-features)
  - [Reference Files](#reference-files)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Features

*   **CLI First**: No UI needed. Manage everything from your terminal.
*   **Provider Agnostic**: Designed for extension, with support for Google Gemini, OpenAI, and Anthropic.
*   **Local Configuration**: Define your custom GPTs in simple JSON files.
*   **Interactive Chat**: Chat with your custom GPTs directly from the command line.
*   **Non-interactive Chat**: Send a single message or a file to a GPT without entering an interactive session.
*   **Reference Files**: Include local files in your GPT's context to provide additional information and data.
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
    OPENAI_API_KEY="your_openai_api_key_here"
    ANTHROPIC_API_KEY="your_anthropic_api_key_here"
    ```
The CLI will automatically load the required API key based on the `provider` specified in your GPT's configuration file.

## Commands

### Command: `new`
Creates a new GPT configuration via an interactive wizard.

```bash
localgpt new my-first-gpt
```

This will launch a step-by-step wizard that asks you for the following information:
*   **Title**: A name for your GPT.
*   **Description**: A short description of what it does.
*   **Provider**: Select a provider from a list of supported options (e.g., `gemini`, `openai`, or `anthropic`).
*   **Model**: Select a model from the chosen provider's available list.
*   **System Prompt**: The core instructions for the GPT. This can be typed directly or pasted into the terminal. The prompt is stored in a `SYSTEM_PROMPT.md` file inside the new GPT's directory.

The wizard will create a new directory named after the slug you provide (e.g., `my-first-gpt/`). This directory will contain your `gpt.json` configuration file and any other related files, like the `SYSTEM_PROMPT.md`.

This approach keeps your custom GPTs organized and makes them easy to track in version control alongside your projects.

### Command: `chat`
Starts an interactive chat session with a specified GPT.

```bash
localgpt chat my-first-gpt
```

**Non-interactive Mode**

You can also send a single message or a file directly to the GPT without starting an interactive session.

**Send a message:**
```bash
localgpt chat my-first-gpt --message "What is the capital of France?"
```

**Send a file's content:**
```bash
localgpt chat my-first-gpt --messageFile "path/to/your/message.md"
```

*In non-interactive mode, the response is sent directly to `stdout`.*

### Command: `models`
Lists all available models from the supported AI providers.

```bash
localgpt models
```

## Getting Started with an Example

This repository includes a `pizza-pro` example to help you get started quickly.

### 1. Manual Configuration

You can create a `[name]/gpt.json` file yourself. Review the `pizza-pro/gpt.json` file for an example of the structure.

[**`pizza-pro/gpt.json`**](https://github.com/jtsternberg/LocalGPT/blob/main/pizza-pro/gpt.json)

```json
{
    "provider": "gemini",
    "title": "Pizza Pro",
    "description": "Pizza lover",
    "model": "gemini-2.5-flash",
    "system_prompt": "./SYSTEM_PROMPT.md",
    "reference_files": [
        "./reference-files/pizza-toppings.md"
    ]
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
```

## Additional Features

### Reference Files

You can provide your GPT with local files to use as reference material. This is useful for providing additional context.

To use reference files, add a `reference_files` array to your `gpt.json` file. The array should contain a list of relative paths to the files you want to include.

```json
{
    "reference_files": [
        "./reference-files/my-file-1.txt",
        "./reference-files/my-file-2.md"
    ]
}
```

When you start a chat session, the content of these files will be loaded and included in the system prompt that is sent to the LLM. This allows the GPT to use the information in the files to inform its responses.
