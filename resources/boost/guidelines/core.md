## obelaw/o-configs

- Runtime settings are stored in the `o_configs` table and read with `oconfig('path', $default)` or `Obelaw\Configs\Facades\OConfig`. Do not edit `.env` or `config/*.php` to change a setting at runtime.
- Reads fall back in this order: current group → global group → Laravel `config()` → default. Do not pass `config('same.path')` as the default.
- Writes (`OConfig::set`, `oconfig([...])`, `forget`) only touch the current group. The class in `config('o-configs.group_resolver')` picks that group, and the default is global.
- Per-model settings use the `Obelaw\Configs\Traits\HasConfigs` trait: `$model->configs()->get/set/has/forget/all()`, stored under group `"{morph class}:{key}"`.
- `oconfig()` does not change `config()`. Copy values with `config([...])` if framework code needs them.
- `ConfigModel` and `ConfigurationModelService` no longer exist (removed in 2.x). Use `HasConfigs` or `OConfig::group(...)`.
- Activate the `o-configs` skill for groups, tenant resolvers, testing and pitfalls.
