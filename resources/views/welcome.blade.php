<!-- 加载编辑器的容器 -->
<div id="ueditor" class="edui-default">
    @include('UEditor::head')
</div>
<!-- 加载编辑器的容器 -->
<script id="container" name="content" type="text/plain">
    这里写你的初始化内容
</script>

<!-- 实例化编辑器 -->
    <script id="ueditor"></script>
    <script>
        var ue=UE.getEditor("ueditor");
        ue.ready(function(){
            //因为Laravel有防csrf防伪造攻击的处理所以加上此行
            ue.execCommand('serverparam','_token','{{ csrf_token() }}');
        });
    </script>
