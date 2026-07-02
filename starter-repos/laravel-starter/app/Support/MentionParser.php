<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Parses `@username` mentions out of free text and resolves them to users.
 *
 * A handle is `@` followed by word characters, provided the `@` is not
 * preceded by a word character — which keeps email locals like `x@alice.com`
 * from being treated as a mention.
 */
class MentionParser
{
    /**
     * Extract unique, lower-cased handles from a body of text.
     *
     * @return array<int, string>
     */
    public function extractHandles(string $body): array
    {
        preg_match_all('/(?<!\w)@([A-Za-z0-9_]+)/u', $body, $matches);

        $handles = array_map('strtolower', $matches[1]);

        return array_values(array_unique($handles));
    }

    /**
     * Resolve the mentions in a body to the existing users they name.
     *
     * @return Collection<int, User>
     */
    public function resolve(string $body): Collection
    {
        $handles = $this->extractHandles($body);

        if ($handles === []) {
            return collect();
        }

        return User::query()
            ->whereIn(DB::raw('lower(username)'), $handles)
            ->get();
    }
}
