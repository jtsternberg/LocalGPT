# LocalGPT - Roadmap

## Other Providers

- [ ] OpenAI
- [ ] Anthropic
- [ ] Gemini
- [ ] Groq
- [ ] Ollama
- [ ] DeepSeek

## Non-interactive Mode

- [ ] Add a `-m`/`--message` and `-f`/`--messageFile` flag to the `chat` command.
  - The message flag is a string, and the messageFile flag is a file path to your message (e.g. a markdown file)
- [ ] If the message flag is set, the chat will be non-interactive, and the response will be printed to the terminal.

## System Prompt Builder

System Prompt BUILDER - part of the `new` command. Works like openai's custom gpt chat interface that interactively builds the system prompt through ongoing conversation.

The final system prompt is saved to a `SYSTEM_PROMPT.md` file, and the file path is saved in the GPT configuration file.

## Reference Files

- [ ] Prompt for local file paths during the `new` command's interactive wizard.
- [ ] Validate that the provided file paths exist.
- [ ] Convert the file to a markdown file, and add it to the `reference-files` directory.
- [ ] Add the relative paths to the `reference_files` array in `gpt.json`.
- [ ] Ensure that the reference files are pre-loaded into the chat, like the persona.
- [ ] Build a meta prompt that explains the reference files and how to use them to the LLM.

## Chat History

Chat History - part of the `chat` command

- [ ] Add a `chat-history.json` file to the GPT's directory, and store each message that is sentin the history.
- [ ] Add a `useHistory` flag to the `chat` command.
- [ ] If the `useHistory` flag is true, load the chat history from the `chat-history.json` file in the GPT's chat. The user should be able to see the history in the chat.

_If the message flag is set, the chat will be non-interactive, and no chat history will be saved._
