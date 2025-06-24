# LocalGPT Examples

The examples here are demonstrations of how to use LocalGPT.

There is an example for each provider, and different configurations.

To test:
- You will need to have already followed the installation instructions in the [README](../README.md#installation--configuration).
- You will need to add your corresponding api key to your `.env` file.
- You will need to `cd` into the `examples` directory (or pass the full path to the `chat` command)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Example 1: Pizza Pro](#example-1-pizza-pro)
- [Example 2: Burger Expert](#example-2-burger-expert)
- [Example 3: Music Guru](#example-3-music-guru)
- [Example 4: Real-estate Guru](#example-4-real-estate-guru)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Example 1: Pizza Pro

The `pizza-pro-gemini-2.5-flash` example is a simple GPT that is an expert on pizza. It uses the Gemini provider and the `gemini-2.5-flash` model.

- [Configuration](./pizza-pro-gemini-2.5-flash/gpt.json)
- [System Prompt](./pizza-pro-gemini-2.5-flash/SYSTEM_PROMPT.md)

Once you have configured your `GEMINI_API_KEY` key, you can start a chat session with this example:

```bash
localgpt chat pizza-pro-gemini-2.5-flash
```

## Example 2: Burger Expert

Another food-expert, this time using the OpenAI provider and the `gpt-3.5-turbo` model.

- [Configuration](./burger-expert-openai-gpt-3.5-turbo/gpt.json)
- [System Prompt](./burger-expert-openai-gpt-3.5-turbo/SYSTEM_PROMPT.md)
- [Reference Files](./burger-expert-openai-gpt-3.5-turbo/reference-files)

Once you have configured your `OPENAI_API_KEY` key, you can start an interactive chat session with this example:

```bash
localgpt chat burger-expert-openai-gpt-3.5-turbo
```

This example comes with a reference file. To see how it works, you can run:

```bash
localgpt chat burger-expert-openai-gpt-3.5-turbo --message "Can you share with me one of your top secret recipes?" --verbose
```

And it's response should include one of the recipes from the [reference file](./burger-expert-openai-gpt-3.5-turbo/reference-files/top-secret-recipes.md).

## Example 3: Music Guru

This example is a specialist in all things music streaming. It uses the Ollama provider and the `llama3:latest` model.

- [Configuration](./music-guru-ollama-llama3-latest/gpt.json)
- [System Prompt](./music-guru-ollama-llama3-latest/SYSTEM_PROMPT.md)

The Ollama provider requires you to have [Ollama](https://ollama.com/) installed and running. No API key is needed.

```bash
localgpt chat music-guru-ollama-llama3-latest
```

## Example 4: Real-estate Guru

This example is a regional real-estate expert. It uses the Anthropic provider and the `claude-3-5-sonnet-20240620` model.

- [Configuration](./real-estate-anthropic-claude-3-5-sonnet-20240620/gpt.json)
- [System Prompt](./real-estate-anthropic-claude-3-5-sonnet-20240620/SYSTEM_PROMPT.md)

Once you have configured your `ANTHROPIC_API_KEY` key, you can find out which region is their speciality:

```bash
localgpt chat real-estate-anthropic-claude-3-5-sonnet-20240620 -m "What region is your speciality?" --verbose
```