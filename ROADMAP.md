# LocalGPT - Roadmap

## Other Providers

- [ ] OpenAI
- [ ] Anthropic
- [ ] Gemini
- [ ] Groq
- [ ] Ollama
- [ ] DeepSeek

## System Prompt Builder

System Prompt BUILDER - part of the `new` command. Works like openai's custom gpt chat interface that interactively builds the system prompt through ongoing conversation.

The final system prompt is saved to a `SYSTEM_PROMPT.md` file, and the file path is saved in the GPT configuration file.

## Reference File Handling

- [ ] Prompt for local file paths during the `new` command's interactive wizard.
- [ ] Validate that the provided file paths exist.
- [ ] Copy the validated files into the GPT's `reference-files` directory.
- [ ] Add the relative paths to the `reference_files` array in `gpt.json`.

## Chat History

Chat History - part of the `chat` command. Works like openai's custom gpt chat interface that shows the chat history.

