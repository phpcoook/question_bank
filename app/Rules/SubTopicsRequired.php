<?php

namespace App\Rules;

use App\Models\SubTopic;
use Illuminate\Contracts\Validation\Rule;

class SubTopicsRequired implements Rule
{
    protected $topics;

    public function __construct($topics)
    {
        $this->topics = $topics;
    }

    public function passes($attribute, $value)
    {

        if (empty($this->topics)) {
            return true;
        }
        $subTopicsFromDb = SubTopic::whereIn('topic_id', $this->topics)->pluck('id')->toArray();
        $selectedSubTopics = collect($value);
        foreach ($this->topics as $topic) {
            $subTopicsForTopic = SubTopic::where('topic_id', $topic)->pluck('id')->toArray();

            if (empty(array_intersect($subTopicsForTopic, $selectedSubTopics->toArray()))) {
                return false;
            }
        }
        return true;
    }

    public function message()
    {
        return 'Each selected topic must have at least one corresponding subtopic selected.';
    }
}
