# LocalGPT - TODO

## Migration to LLPhant

- [ ] Install `llphant/llphant` via Composer
- [ ] Add provider configuration to project (API keys in `.env` file)
- [ ] Refactor `src/Service/` to use LLPhant's unified API:
   - [ ] **Note**: Do not replace `ProviderFactory`, `ProviderInterface`, and `GeminiProvider`. The goal is to call the LLPhant library when applicable under the hood.
   - [ ] Update `GeminiProvider` to use the LLPhant library internally for its chat functionality. This will likely involve using an `LLPhant\Chat\` class.
- [ ] Refactor `src/Command/ChatCommand.php`:
   - [ ] Use an LLPhant Chat instance for chat responses.
   - [ ] Pass conversation history using LLPhant's message objects.
   - [ ] Handle system prompts as the initial message.
- [ ] Refactor `src/Command/ListModelsCommand.php`:
   - [ ] Investigate and implement model listing for each provider using LLPhant's capabilities, or hard-code them for now (or use the already-existing solution for Gemini).
- [ ] Refactor `src/Command/NewCommand.php`:
   - [ ] Use LLPhant's provider/model selection mechanisms.
- [ ] Update documentation to reflect LLPhant usage (README, examples)
   - [ ] Document new `.env` variables required by LLPhant.
   - [ ] Update language to mention LLPhant and multi-provider support.
- [ ] Add tests using mocks for LLPhant responses.

## LLPhant-Related Future Enhancements

- [ ] Add support for more providers via LLPhant (OpenAI, Anthropic, Groq, Ollama, DeepSeek).
- [ ] Expose a public PHP API for using LocalGPT, with LLPhant as the backend.
- [ ] Add non-interactive mode to the `chat` command, using LLPhant for single-shot responses.
- [ ] Create a "System Prompt Builder" agent using LLPhant to interactively define a GPT's persona.
- [ ] Implement reference file handling by using LLPhant's embedding and retrieval features.
- [ ] Implement chat history persistence using LLPhant's chat history features.
- [ ] Leverage LLPhant's core feature: structured data extraction to get typed objects back from the LLM.
- [ ] Implement function calling by defining tools as classes and using LLPhant's features.
