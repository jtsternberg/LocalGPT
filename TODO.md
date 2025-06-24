# LocalGPT - TODO

## Dynamic System Prompt (Replacements)

- [ ] **Config Model (`src/Models/Config.php`):**
    - [ ] Add `custom_fields` property.
    - [ ] Add `getCustomFields()` method to retrieve the configuration.
    - [ ] Add `setCustomFieldValue(string $key, string $value)` to update/store field values at runtime.
    - [ ] In the constructor or a loading method, correctly parse the `custom_fields` array from `gpt.json`.

- [ ] **Provider (`src/Provider/BaseProvider.php`):**
    - [ ] Add a protected property `protected array $customFieldValues = [];`.
    - [ ] Add a public method `setCustomFieldValues(array $values)`.
    - [ ] In `setConfig(Config $config)`, call `setCustomFieldValues()` with any values from the config.
    - [ ] In `buildSystemPrompt()`, iterate through `$customFieldValues` and replace `{{key}}` placeholders in `$this->systemPrompt` with their corresponding values.

- [ ] **Chat Command (`src/Command/ChatCommand.php`):**
    - [ ] **Argument Parsing:**
        - [ ] Define a new `--set` option that accepts key-value pairs (e.g., `--set pizza_region="New York"`). This option should be repeatable for multiple fields.
        - [ ] In `execute()`, parse all `--set` arguments and store them.
    - [ ] **Value Resolution Logic:**
        - [ ] In `execute()`, after loading the GPT config, determine the final value for each custom field by checking in this order of priority:
            1.  Value from `--set` command-line argument.
            2.  Pre-defined `value` in `gpt.json`.
            3.  Value from interactive prompt.
            4.  `default` value from `gpt.json`.
    - [ ] **Interactive Prompts:**
        - [ ] For each custom field that still needs a value, prompt the user interactively.
        - [ ] Use Symfony Question Helper's `Question` for `text` type.
        - [ ] Use Symfony Question Helper's `ChoiceQuestion` for `dropdown` type.
    - [ ] **Provider Integration:**
        - [ ] Collect all final custom field values.
        - [ ] Pass the values to the provider instance using `setCustomFieldValues()`.

## LLPhant-Related Future Enhancements

- [ ] Add support for more providers via LLPhant
  - [X] OpenAI
  - [X] Anthropic
  - [ ] Groq
  - [ ] Ollama
  - [ ] DeepSeek
  - [ ] Grok
  - [ ] Grok 2
- [ ] Expose a public PHP API for using LocalGPT, with LLPhant as the backend.
- [ ] Create a "System Prompt Builder" agent using LLPhant to interactively define a GPT's persona.
- [ ] Implement reference file handling by using LLPhant's embedding and retrieval features.
- [ ] Implement chat history persistence using LLPhant's chat history features.

