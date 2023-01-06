<?php

declare(strict_types=1);

/**
 * @var App\Sessions\Session $session
 * @var App\Sites\Input\SiteInput $input
 * @var App\Languages\Language $lang
 * @var App\Sites\TemplatesEngine\Page $page
 *
 * @var Kuvardin\FastMysqli\SelectionData $selection_data
 */

$current_page = $selection_data->getPage();
$total_pages = $selection_data->getPagesNumber();

$pages = [1, $total_pages];
for ($i = $current_page - 3; $i <= $current_page + 3; $i++) {
    if ($i > 1 && $i < $total_pages) {
        $pages[] = $i;
    }
}

sort($pages);

?>

<nav aria-label="pagination">
    <ul class="pagination mb-0 justify-content-center">

        <?php if ($current_page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= $input->getParamsAsString(['page' => $current_page - 1]) ?>">
                    <?= $lang->require('go_to_previous_page') ?>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                    <?= $lang->require('go_to_previous_page') ?>
                </a>
            </li>
        <?php endif; ?>

        <?php foreach ($pages as $i): ?>
            <?php if ($i < 1 || $i > $total_pages) continue; ?>
            <?php if ($i === $current_page): ?>
                <li class="page-item active" aria-current="page">
                    <a class="page-link" href="#"><?= $i ?></a>
                </li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= $input->getParamsAsString(['page' => $i]) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($current_page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= $input->getParamsAsString(['page' => $current_page + 1]) ?>">
                    <?= $lang->require('go_to_next_page') ?>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                    <?= $lang->require('go_to_next_page') ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
