<?php
/**
 * @var yii\web\View $this
 */
$this->title = '发起上线';
use yii\widgets\ActiveForm;
use app\models\Project;

?>
<div class="box">
    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
      <div class="box-body">
        <?= $form->field($task, 'title')->label('任务标题', ['class' => 'control-label bolder blue']) ?>

        <!-- 分支选取 -->
        <?php if ($conf->repo_mode == Project::REPO_BRANCH) { ?>
          <div class="form-group">
              <label class="control-label bolder blue">选取分支
                  <a class="show-tip icon-refresh green" href="javascript:;"></a>
                  <span class="tip">查看所有分支</span>
                  <i class="get-branch icon-spinner icon-spin orange bigger-125" style="display: none"></i>
              </label>
              <select name="Task[branch]" aria-hidden="true" tabindex="-1" id="branch" class="form-control select2 select2-hidden-accessible">
                  <option value="truck">trunk</option>
              </select>
          </div>
        <?php } ?>
        <!-- 分支选取 end -->

          <?= $form->field($task, 'file_list')
              ->textarea([
                  'placeholder'    => 'index.php  1234',
                  'data-placement' => 'top',
                  'data-rel'       => 'tooltip',
                  'data-title'     => '所有目标机器都部署完毕之后，做一些清理工作，如删除缓存、重启服务（nginx、php、task），一行一条(双引号将会被转义为\")',
                  'style'          => 'overflow:scroll;overflow-y:hidden;;overflow-x:hidden',
              ])
              ->label('文件列表', ['class' => 'control-label bolder blue']) ?>
      </div><!-- /.box-body -->

      <div class="box-footer">
        <input type="submit" class="btn btn-primary" value="提交">
      </div>

    <!-- 错误提示-->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="800px">
                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        发生了错误
                    </h4>
                </div>
                <div class="modal-body"></div>
            </div><!-- /.modal-content -->
        </div>

    </div>
    <!-- 错误提示-->

    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    jQuery(function($) {
        function getBranchList() {
            $('.get-branch').show();
            $('.tip').hide();
            $('.show-tip').hide();
            $.get("/walle/get-branch?projectId=" + <?= (int)$_GET['projectId'] ?>, function (data) {
                // 获取分支失败
                if (data.code) {
                    showError(data.msg);
                }
                var select = '';
                $.each(data.data, function (key, value) {
                    // 默认选中 trunk 主干
                    var checked = value.id == 'trunk' ? 'selected' : '';
                    select += '<option value="' + value.id + '"' + checked + '>' + value.message + '</option>';
                })
                $('#branch').html(select);
                $('.get-branch').hide();
                $('.show-tip').show();
            });
        }
//
//        function getCommitList() {
//            $.get("/walle/get-commit-history?projectId=" + <?//= (int)$_GET['projectId'] ?>// +"&branch=" + $('#branch').val(), function (data) {
//                // 获取commit log失败
//                if (data.code) {
//                    showError(data.msg);
//                }
//
//                var select = '';
//                $.each(data.data, function (key, value) {
//                    select += '<option value="' + value.id + '">' + value.message + '</option>';
//                })
//                $('#task-commit_id').html(select);
//                $('.get-history').hide()
//            });
//        }

        $('#branch').change(function() {
            $('.get-history').show();
//            getCommitList();
        })

        // 页面加载完默认拉取trunk
        getBranchList();

        // 查看所有分支提示
        $('.show-tip')
            .hover(
            function() {
                $('.tip').show()
            },
            function() {
                $('.tip').hide()
            })
            .click(function() {
                getBranchList();
            })

        // 错误提示
        function showError($msg) {
            $('.modal-body').html($msg);
            $('#myModal').modal({
                backdrop: true,
                keyboard: true,
                show: true
            });
        }

        // 清除提示框内容
        $("#myModal").on("hidden.bs.modal", function () {
            $(this).removeData("bs.modal");
        });
    })

</script>