<?php
$url = $this->di->get("url");
?>

<div class="question-post">
    <h2>
        <a href="<?= $url->create("question/" . $question->id) ?>">
            <?= $question->title; ?>
        </a>
    </h2>
    <div class="question-content">
        <?= $question->content; ?>
    </div>
    <div class="gravatar">
        <img src="<?= $question->gravatar; ?>">
    </div>
    <div class="author">
        Skriven av: <?= $question->firstname . " " . $question->lastname; ?>
    </div>
    <?php if ($question->owner || $question->userAdmin) : ?>
    <div class="edit">
        <a href="<?= $url->create("question/edit/" . $question->id); ?>">
            Redigera
        </a> -
        <a href="question/delete/<?= $question->id ?>">
            Radera
        </a>
    </div>
    <?php endif; ?>
    <hr>
    <?php if ($this->regionHasContent("awnser")) : ?>
    <div class="awnsers">
        <h2>Svar</h2>
        <hr>
        <?php $this->renderRegion("awnser") ?>
    </div>
    <?php endif; ?>
</div>
