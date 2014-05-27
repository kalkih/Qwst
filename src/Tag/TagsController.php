<?php

namespace Anax\Tag;

/**
* Tag Controller class
*/
class TagsController implements \Anax\DI\IInjectionAware
{

    use \Anax\DI\TInjectable;

    public function initialize()
    {
        $this->tags = new \Anax\Tag\Tag();
        $this->tags->setDI($this->di);
    }

    public function indexAction()
    {
        $this->initialize();

        $tags = $this->tags->findAll();

        $this->theme->setTitle('Tags');
        $this->views->add('tags/list', [
            'title' => 'Tags',
            'tags' => $tags,
        ], 'main');

        $this->dispatcher->forward([
            'controller' => 'questions',
            'action'     => 'sidebar',
        ]);
    }

    public function popularAction($area = 'main', $limit = 5)
    {
        $this->initialize();

        $tags = $this->tags->findByLimit($limit);

        $this->views->add('tags/list', [
            'title' => 'Popular tags',
            'tags' => $tags,
        ], $area);

    }

    public function tagAction($tag = null)
    {
        $this->initialize();

        $tag = $this->tags->findByTag($tag);

        if (empty($tag)) {
            $this->views->add('default/error', [
                'content' => 'Tag does not exist',
                'details' => 'Tag is deleted or does not exist.',
                'title' => '404 - Tag not found!',
            ], 'full');
        } else {
            $all = $this->dispatcher->forward([
                'controller' => 'questions',
                'action' => 'question',
            ]);

            $questions = null;

            foreach ($all as $one) {
                $tags = unserialize($one->tags);

                if (!is_bool($tags)) {
                    if (in_array($tag->text, $tags)) {
                        $questions[] = $one;
                    }
                }
            }

            $this->theme->setTitle('#' . $tag->text);
            $this->views->add('tags/tag', [
                'title' => '#' . $tag->text,
                'questions' => $questions,
            ], 'main');

            $this->dispatcher->forward([
                'controller' => 'questions',
                'action'     => 'sidebar',
            ]);
        }
    }

    public function check($tags = [])
    {
        $this->initialize();

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if ($this->tags->findByTag($tag)) {
                    $this->update($tag);
                } else {
                    $this->add($tag);
                }
            }
        }
    }

    private function update($tag)
    {
        $this->initialize();
        $tag = $this->tags->findByTag($tag);
        $tag->uses++;
        $tag->saveReal();
    }

    private function add($tag)
    {
        if ($tag != "") {
            $this->tags->save([
                'text' => $tag,
                'uses' => '1',
            ]);
        }
    }

    public function setupAction()
    {
        $this->initialize();

        $this->theme->setTitle('Setup');

        $this->db->dropTableIfExists('tag')->execute();

        $this->db->createTable(
            'tag',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
                'text' => ['varchar(16)', 'not null'],
                'uses' => ['integer', 'default "0"'],
            ]
        )->execute();

        $this->views->addString('<h1>Tag database was successfully setup!</h1>', 'main');
    }

}
