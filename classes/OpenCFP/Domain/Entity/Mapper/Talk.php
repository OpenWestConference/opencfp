<?php

namespace OpenCFP\Domain\Entity\Mapper;

use Spot\Mapper;

class Talk extends Mapper
{
    /**
     * Return an array of talks for use by PagerFanta where we set the
     * value in the favorite based on whether or not the current admin
     * user has favourited this talk
     *
     * @param  integer $admin_user_id
     * @return array
     */
    public function getAllPagerFormatted($admin_user_id, $sort, $app)
    {
        $talks = $this->all()
            ->order($sort)
            ->with(['favorites']);
        $formatted = array();

        foreach ($talks as $talk) {
            $formatted[] = $this->createdFormattedOutput($talk, $admin_user_id, $app);
        }

        return $formatted;
    }

    /**
     * Return an array of recent talks
     *
     * @param  integer $admin_user_id
     * @param  integer $limt
     * @return array
     */
    public function getRecent($admin_user_id, $limit = 10)
    {
        $talks = $this->all()
            ->order(['created_at' => 'DESC'])
            ->with(['favorites', 'speaker'])
            ->limit($limit);
        $formatted = [];

        foreach ($talks as $talk) {
            $formatted[] = $this->createdFormattedOutput($talk, $admin_user_id);
        }

        return $formatted;
    }

    /**
     * Return a collection of talks that a majority of the admins have liked
     *
     * @param  integer $admin_majority
     * @return array
     */
    public function getAdminFavorites($admin_user_id, $admin_majority)
    {
        $talks = $this->all()
            ->order(['created_at' => 'DESC'])
            ->with(['favorites']);

        $favorite_talks = [];

        foreach ($talks as $talk) {
            if ($talk->favorites->count() >= $admin_majority) {
                $favorite_talks[] = $talk;
            }
        }

        $formatted = [];

        foreach ($favorite_talks as $talk) {
            $formatted[] = $this->createdFormattedOutput($talk, $admin_user_id);
        }

        return $formatted;
    }

    /**
     * Return a collection of entities representing talks that belong to a
     * specific user
     *
     * @param  integer $user_id
     * @return array
     */
    public function getByUser($user_id)
    {
        return $this->where(['user_id' => $user_id]);
    }

    /**
     * Iterates over DBAL objects and returns a formatted result set
     *
     * @param  mixed   $talk
     * @param  integer $admin_user_id
     * @return array
     */
    protected function createdFormattedOutput($talk, $admin_user_id, $app = false)
    {
        if ($talk->favorites) {
            foreach ($talk->favorites as $favorite) {
                if ($favorite->admin_user_id == $admin_user_id) {
                    $talk->favorite = 1;
                }
            }
        }

        $output = [
            'id' => $talk->id,
            'title' => $talk->title,
            'type' => $talk->type,
            'category' => $talk->category,
            'created_at' => $talk->created_at,
            'vote_average' => $talk->vote_average,
            'vote_count' => $talk->vote_count,
            'selected' => $talk->selected,
            'favorite' => $talk->favorite
        ];

        if ($talk->speaker) {
            $selected = 0;
            if ($app) {
                $result = $app['db']->query("SELECT COUNT(*) FROM talks WHERE user_id=".$talk->speaker->id." AND selected=1");
                $selected = $result->fetchColumn(0);
            }
            
            $output['user'] = [
                'id' => $talk->speaker->id,
                'first_name' => $talk->speaker->first_name,
                'last_name' => $talk->speaker->last_name,
                'airport' => $talk->speaker->airport,
                'transportation' => $talk->speaker->transportation,
                'max_presentations' => $talk->speaker->max_presentations,
                'selected' => $selected,
            ];
        }

        return $output;
    }

}
