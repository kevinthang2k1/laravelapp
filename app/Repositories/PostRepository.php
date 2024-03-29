<?php

namespace App\Repositories;

use App\Models\Post;

use App\Repositories\Interfaces\PostRepositoryInterface;

use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    protected $model;

    public function __construct(
        Post $model
    ){
        $this->model = $model;
    }

    public function pagination(
        array $column = ['*'], 
        array $condition = [], 
        int $perPage = 1, 
        array $extend =[],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = [],
        ){
        $query = $this->model->select($column)->where(function($query) use ($condition) {
            if(isset($condition['keyword']) && !empty($condition['keyword'])){
                $query->where('name', 'LIKE', '%'.$condition['keyword'].'%');
            }

            if(isset($condition['publish']) && $condition['publish'] != 0 ){
                $query->where('publish', '=', $condition['publish']);
            }
            return $query;
        });

        if(isset($relation) && !empty($relation)){
            foreach($relation as $relation){
                $query->withCount($relation);
            }
        }

        if(isset($join) && is_array($join) && count($join)){
            foreach($join as $key => $val){
                $query->join($val[0], $val[1], $val[2], $val[3]);
            }
        }

        // dd($orderBy);
        if(isset($orderBy) && !empty($orderBy)){
            $query->orderBy($orderBy[0], $orderBy[1]);
        }
        

        return $query->paginate($perPage)
                    ->withQueryString()->withPath(env('APP_URL').$extend['path']);
    }

    public function getPostById(int $id = 0, $language_id = 0  ){
        return $this->model->select([
                                'posts.id',
                                'posts.post_catalogue_id',
                                'posts.image',
                                'posts.icon',
                                'posts.album',
                                'posts.publish',
                                'posts.follow',
                                'tb2.name',
                                'tb2.description',
                                'tb2.content',
                                'tb2.meta_title',
                                'tb2.meta_keyword',
                                'tb2.meta_description',
                                'tb2.canonical',
                            ])
                            ->join('post_language as tb2','tb2.post_id','=','posts.id')
                            ->with('post_catalogues')
                            ->where('tb2.language_id', '=', $language_id)
                            ->findOrFail($id);
    }
}
