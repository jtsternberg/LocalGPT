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

## Dynamic System Prompt (Replacements)

This feature allows for dynamic placeholders in the `SYSTEM_PROMPT.md` file, which are replaced by values from custom fields defined in the `gpt.json` file. This makes GPTs more reusable and configurable.

For example, a placeholder like `{{pizza_region}}` in the system prompt can be replaced with "San Francisco", "Chicago", or "New York" at runtime.

### Implementation Steps

1.  **Update `gpt.json` configuration:** Add a `custom_fields` array to `gpt.json` to define the available fields. Each field will have a `name` for display, a `key` for the placeholder, a `type` (e.g., `text`, `dropdown`), and other relevant options. If a `value` is provided for a field, it will be used as the value for the placeholder, and the user will not be prompted for it.

    **Example `gpt.json`:**
    ```json
    {
        "title": "Pizza Pro",
        "system_prompt": "./SYSTEM_PROMPT.md",
        "custom_fields": [
            {
                "name": "Pizza Region",
                "key": "pizza_region",
                "type": "dropdown",
                "default": "Chicago",
                "options": [
                    "Chicago",
                    "New York",
                    "San Francisco"
                ]
            }
        ]
    }
    ```
    (Setting a `value` for a custom field will prevent the user from being prompted for it.)

2.  **Update System Prompt:** Use double curly brace syntax `{{key}}` in `SYSTEM_PROMPT.md` to define placeholders.

    **Example `SYSTEM_PROMPT.md`:**
    ```markdown
    You are PizzaGPT, the ultimate pizza enthusiast and culinary expert, especially in regards to the specific region of {{pizza_region}}.
    ```

3.  **Implement placeholder replacement:**
    - In the `chat` command, before starting the session, check for `custom_fields` in `gpt.json`.
    - **Handle command-line arguments:** Allow setting custom field values via command-line arguments (e.g., `--set pizza_region="New York"`). This will override any `value` set in `gpt.json`.
    - **Handle interactive prompts:** For any custom field that doesn't have a value from a command-line argument or a `value` in `gpt.json`, prompt the user for input. Use the `default` value if available.
    - Modify `BaseProvider::buildSystemPrompt()` to replace the placeholders in the system prompt text with the collected values before sending it to the LLM.

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

## Function Calling

- [ ] Implement function calling
