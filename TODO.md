# LocalGPT - TODO

## Migration to LLPhant

- [x] Install `llphant/llphant` via Composer
- [x] Add provider configuration to project (API keys in `.env` file)
- [x] Refactor `src/Service/` to use LLPhant's unified API:
   - [x] **Note**: Do not replace `ProviderFactory`, `ProviderInterface`, and `GeminiProvider`. The goal is to call the LLPhant library when applicable under the hood.
   - [ ] Update `GeminiProvider` to use the LLPhant library internally for its chat functionality. This will likely involve using an `LLPhant\Chat\` class.
- [x] Refactor `src/Command/ChatCommand.php`:
   - [x] Use an LLPhant Chat instance for chat responses.
   - [x] Pass conversation history using LLPhant's message objects.
   - [x] Handle system prompts as the initial message.
- [x] Refactor `src/Command/NewCommand.php`:
   - [x] Use LLPhant's provider/model selection mechanisms.
- [ ] Update documentation to reflect LLPhant usage (README, examples), only if necessary.
   - [ ] Document new `.env` variables required by LLPhant.
   - [ ] Update multi-provider support.

## LLPhant-Related Future Enhancements

- [ ] Add support for more providers via LLPhant (OpenAI, Anthropic, Groq, Ollama, DeepSeek).
- [ ] Expose a public PHP API for using LocalGPT, with LLPhant as the backend.
- [ ] Add non-interactive mode to the `chat` command, using LLPhant for single-shot responses.
- [ ] Create a "System Prompt Builder" agent using LLPhant to interactively define a GPT's persona.
- [ ] Implement reference file handling by using LLPhant's embedding and retrieval features.
- [ ] Implement chat history persistence using LLPhant's chat history features.
- [ ] Leverage LLPhant's core feature: structured data extraction to get typed objects back from the LLM.
- [ ] Implement function calling by defining tools as classes and using LLPhant's features.
