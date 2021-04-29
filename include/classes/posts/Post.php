<?php
/**
 * File:          Post.php
 * File Created:  2021/04/05 23:54
 * Modified By:   Gregory Schoeman <gregory@secondsite.xyz>
 * PHP version 8.0
 * -----
 *
 * @category  WebApp
 * @package   NPM
 * @author    Gregory Schoeman <gregory@secondsite.xyz>
 * @copyright 2019-2021 SecondSite
 * @license   https://opensource.org/licenses/MIT  MIT
 * @version   GIT: <1.0.0>
 * @link      https://github.com/SecondSite-web/dash.git
 * @project   dash
 */

namespace Dash\Post;

use PDO;
use PDOException;

class Post
{
    private object $pdo;
    public array $posts;
    public array $meta;
    /**
     * Post constructor.
     * @param $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        if (!$this->pdo->query("SHOW TABLES LIKE 'dash_posts'")->fetchAll()) {
            $install = new Install($this->pdo);
            $install->init();
        }
    }

    /**
     * Returns an unfiltered array of all posts
     *
     * @return array
     */
    public function getAllPosts(): array
    {
        return $this->pdo->query(
            "SELECT * FROM dash_posts"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets the post by request from the slug
     *
     * @param $postUrl
     * @return mixed
     */
    public function getPostBySlug($postUrl): mixed
    {
        $stmt = $this->pdo->prepare("SELECT * FROM dash_posts WHERE post_url=:post_url LIMIT 1");
        $stmt->execute(['post_url' => $postUrl]);
        return $stmt->fetch(PDO::FETCH_NAMED);
    }

    /**
     * Gets the post meta matching the post id
     *
     * @param $postId
     * @return mixed
     */
    public function getPostMetaById($postId): mixed
    {
        $stmt = $this->pdo->prepare("SELECT `meta_key`, `meta_value` FROM dash_post_meta WHERE post_id=:post_id");
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Returns an unfiltered array of all meta
     *
     * @return array
     */
    public function getAllMeta(): array
    {
        return $this->pdo->query(
            "SELECT * FROM dash_post_meta"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Set Magic method to save post and post meta data
     *
     * @param $post
     * @param $meta
     */
    public function savePost($post, $meta): void
    {
        $postId = $this->postDb($post);
        $this->savePostMeta($meta, $postId);
    }

    /**
     * Returns post meta matching the post Id
     *
     * @param $postId
     * @return mixed
     */
    public function getPostById($postId): mixed
    {
        $stmt = $this->pdo->prepare("SELECT * FROM dash_posts WHERE post_id=:post_id");
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Saves Post Data to the Database
     *
     * @param $post
     * @return int
     */
    public function postDb($post): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO dash_posts (post_title, post_content, post_status, post_type, 
        post_author, post_url, modified_time)
VALUES (:post_title, :post_content, :post_status, :post_type, :post_author, :post_url, :modified_time)'
        );
        $stmt->bindParam(':post_title', $post['post_title']);
        $stmt->bindParam(':post_content', $post['post_content']);
        $stmt->bindParam(':post_status', $post['post_status']);
        $stmt->bindParam(':post_type', $post['post_type']);
        $stmt->bindParam(':post_author', $post['post_author']);
        $stmt->bindParam(':post_url', $post['post_url']);
        $stmt->bindParam(':modified_time', $post['modified_time']);
        $stmt->execute();
        return $this->pdo->lastInsertId('post_id');
    }

    /**
     * Foreach loop to save each meta item in array - 1 Post many Meta
     *
     * @param $meta
     * @param $post
     */
    public function savePostMeta($meta, $post): void
    {
        foreach ($meta as $row) {
            $this->saveMeta($row, $post);
        }
    }

    /**
     * Saves new post meta to Post Meta DB
     *
     * @param $meta
     * @param $postId
     * @return bool
     */
    public function saveMeta($meta, $postId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO dash_post_meta (post_id, meta_key, meta_value)
    VALUES (:post_id, :meta_key, :meta_value)'
            );
            $stmt->bindParam(':post_id', $postId);
            $stmt->bindParam(':meta_key', $meta['meta_key']);
            $stmt->bindParam(':meta_value', $meta['meta_value']);
            $stmt->execute();
            return true;
        } catch (PDOException) {
            return false;
        }
    }
}
