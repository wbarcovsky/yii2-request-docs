<?php

/**
 * @var \yii\web\View $this
 * @var $requests array
 * @var $open boolean
 */

use wbarcovsky\yii2\request_docs\assets\DocsAsset;
use yii\helpers\Url;

function getRequestClass($request)
{
    if (strtoupper($request['method']) === 'GET') {
        return 'is-success';
    }
    if (strtoupper($request['method']) === 'DELETE') {
        return 'is-danger';
    }
    if (strtoupper($request['method']) === 'POST') {
        return 'is-info';
    }
    if (strtoupper($request['method']) === 'PUT' || strtoupper($request['method']) === 'PATCH') {
        return 'is-warning';
    }
    return '';
}

DocsAsset::register($this);

?>

<html>
<head>
  <?php $this->head(); ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
  <title><?= $title ?></title>
</head>
<?php $this->beginBody(); ?>
<body data-full-info-url="<?= Url::toRoute('full-info');?>">
<div class="container">
  <br/>
  <h1 class="title"><a href="<?= Url::base() . '?' ?>"><?= $title ?></a></h1>
  <hr/>

  <section class="section">
    <div class="control">
      <form method="get">
        <input class="input is-success" name="search" type="text" placeholder="Search" value="<?= $search ?>"/>
      </form>
    </div>
    <br/>
      <?php foreach ($requests as $request): ?>
        <div class="box <?= $open ? 'box-open' : 'box-close' ?>">
          <div class="level pointer" onclick="toggleBox(this.parentElement)">
            <h4 class="title is-spaced is-5">
              <span class="tag <?= getRequestClass($request) ?>"><?= $request['method'] ?></span>
              <a
                href="<?= Url::base() . '?' . $request['method'] . '+' . $request['url'] ?>"><?= $request['url']; ?></a>
            </h4>
            <h6 class="subtitle is-6"><?= $request['title']; ?></h6>
            <i class="toggle fa fa-chevron-down level-right"></i>
            <i class="toggle fa fa-chevron-up level-right"></i>
          </div>
          <div class="data">
            <div class="tabs">
              <ul>
                <li class="is-active"><a href="javascript:void(0)" onclick="selectTab(this, 'params')">Request Data</a>
                </li>
                <li><a href="javascript:void(0)" onclick="selectTab(this, 'result')">Response</a></li>
                <li><a href="javascript:void(0)" onclick="loadParams(this, '<?= $request['hash'] ?>')">Request Examples</a></li>
                <li><a href="javascript:void(0)" onclick="loadParams(this, '<?= $request['hash'] ?>', true)">Response Examples</a></li>
              </ul>
            </div>
            <table class="table is-fullwidth tab-content params">
              <tbody>
              <?php foreach ($request['params'] as $field => $type): ?>
                <tr>
                  <td><?= $field ?></td>
                  <td><b><?= ucfirst($type) ?></b></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
            <table class="table is-fullwidth tab-content hide result">
              <tbody>
              <?php foreach ($request['result'] as $field => $type): ?>
                <tr>
                  <td><?= $field ?></td>
                  <td><b><?= ucfirst($type) ?></b></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
            <div class="tab-content hide params-example"></div>
            <div class="tab-content hide result-example"></div>
            <div class="load hide">
              <i class="fa fa-spinner fa-pulse fa-2x"></i>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($requests)): ?>
        <div class="notification is-warning">
        <span>
          Requests not found <a style="margin-left: 0.3em;" href="<?= Url::base() . '?' ?>"><i
              class="fa fa-refresh"></i> Refresh</a>
        </span>
        </div>
      <?php endif; ?>
  </section>
</div>
</body>
<?php $this->endBody(); ?>
</html>
<?php
$this->endPage();