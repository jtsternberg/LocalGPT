# LocalGPT - Roadmap

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [LocalGPT - Roadmap](#localgpt---roadmap)
  - [Other Providers](#other-providers)
  - [PHP Library](#php-library)
  - [Non-interactive Mode](#non-interactive-mode)
  - [System Prompt Builder](#system-prompt-builder)
  - [Reference Files](#reference-files)
  - [Chat History](#chat-history)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Other Providers

- [ ] OpenAI
- [ ] Anthropic
- [ ] Gemini
  - [ ] Refactor `GeminiProvider` to use `LLPhant\Chat\GeminiChat` once [PR #264](https://github.com/LLPhant/LLPhant/pull/264) is merged and released.
- [ ] Groq
- [ ] Ollama
- [ ] DeepSeek

## PHP Library

- [ ] Expose a public PHP API for using LocalGPT in your own PHP projects.

## Non-interactive Mode

- [x] Add a `-m`/`--message` and `-f`/`--messageFile` flag to the `chat` command.
  - The message flag is a string, and the messageFile flag is a file path to your message (e.g. a markdown file)
- [x] If the message flag is set, the chat will be non-interactive, and the response will be printed to the terminal.

## System Prompt Builder

System Prompt BUILDER - part of the `new` command. Works like openai's custom gpt chat interface that interactively builds the system prompt through ongoing conversation.

The final system prompt is saved to a `SYSTEM_PROMPT.md` file, and the file path is saved in the GPT configuration file.

Maybe we could use https://github.com/llm-agents-php/agents to build an agent for doing this?

## Reference Files

- [ ] Prompt for local file paths during the `new` command's interactive wizard.
- [ ] Validate that the provided file paths exist.
- [ ] Convert the file to a markdown file, and add it to the `reference-files` directory.
- [ ] Add the relative paths to the `reference_files` array in `gpt.json`.
- [X] Ensure that the reference files are pre-loaded into the chat, like the persona.
- [X] Build a meta prompt that explains the reference files and how to use them to the LLM.
- [X] Implement reference file handling:
  - [X] **MVP:** Initially, load the full content of reference files directly into the chat context.
  - [ ] **Future:** Transition to using embeddings and a vector store for more efficient and scalable retrieval of relevant information from reference files.
    - [ ] **Note:** LLPhant's upcoming Gemini support ([PR #264](https://github.com/LLPhant/LLPhant/pull/264)) includes a `GeminiEmbeddingGenerator`. This will be key to implementing embeddings for the Gemini provider.

## Chat History

Chat History - part of the `chat` command

- [ ] Add a `chat-history.json` file to the GPT's directory, and store each message that is sentin the history.
- [ ] Add a `useHistory` flag to the `chat` command.
- [ ] If the `useHistory` flag is true, load the chat history from the `chat-history.json` file in the GPT's chat. The user should be able to see the history in the chat.

_If the message flag is set, the chat will be non-interactive, and no chat history will be saved._
