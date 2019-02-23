<div class="fixed-row"><input type="button" value="delete" id="exe" class="btn"></div>
<ul class="list">
   <?php for ($i = 0; $i < count($list['items']); ++$i) {
      echo sprintf('
         <li class="list-item">
            <p class="title">%s</p>
            <input type="checkbox" class="cbDel" value="%s">
            <a class="btn" href="%s">update</a>
         </li>', $list['items'][$i]['snippet']['title'], $list['items'][$i]['snippet']['resourceId']['videoId'], baseUrl('video/update/'.$list['items'][$i]['snippet']['resourceId']['videoId']) );
   } ?>
</ul>
<ul class="pagination">
   <?php
   if (isset($list['prevPageToken']))
      echo sprintf('<li><a href="%s" class="btn">previous</a></li>', baseUrl('video/index/'.$list['prevPageToken']) );
   if (isset($list['nextPageToken']))
      echo sprintf('<li><a href="%s" class="btn">next</a></li>', baseUrl('video/index/'.$list['nextPageToken']) );
   ?>
</ul>
<div class="notif"></div>
<script>
   document.getElementById('exe').addEventListener('click', function() {
      let id = document.getElementsByClassName('cbDel'), aid = [];

      for (let i = id.length - 1; i >= 0; i--)
         id[i].checked && aid.push(id[i].value);

      if (aid == false) {
         message('Choose some videos to delete');
         return;
      }
      else if (aid.length == 1) aid = aid[0];

      let xhr = new XMLHttpRequest();
      xhr.open('GET', '<?= baseUrl('video/delete/') ?>' + JSON.stringify(aid) );
      xhr.send();

      xhr.onreadystatechange = function() {
         if (this.readyState == 4) {
            switch (this.status) {
               case 200:
                  message(JSON.parse(this.responseText).success);
                  for (let i = id.length - 1; i >= 0; i--)
                     id[i].checked && id[i].closest('li').remove();
                  break;
               default:
                  message(JSON.parse(this.responseText).error);
                  break;
            }
         }
      };
   });

   function message(txt) {
      let msg = document.getElementsByClassName('notif')[0];
      msg.innerHTML = txt;
      msg.classList.add('show');
      msg.addEventListener('animationend', function() {
         this.classList.remove('show');
      });
   }
</script>