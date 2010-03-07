<div id="box">
      <div class="block" id="block-signup">
        <h2>Feedback și raportarea erorilor</h2>
        <div class="content">
        <?=flash_h($flash);?>
          <form action="/?contact" method="post" class="form">
            <div class="group wat-cf">
              <div class="left">
                <label class="label">Nume</label>
              </div>
              <div class="right">
                <input type="text" class="text_field" name="c[nume]" value="<?=$c['nume'];?>" />
                <span class="description">Câmp obligatoriu. Ex: Ion Popescu</span>
              </div>
            </div>
            <div class="group wat-cf">
              <div class="left">
                <label class="label">Cont SINU</label>
              </div>
              <div class="right">
                <input type="text" class="text_field" name="c[cont]" value="<?=$c['cont'];?>" />
                <span class="description">Numele de utilizator folosit pe SINU.</span>
              </div>
            </div>
            <div class="group wat-cf">
              <div class="left">
                <label class="label">Email</label>
              </div>
              <div class="right">
                <input type="text" class="text_field" name="c[email]" value="<?=$c['email'];?>" />
                <span class="description">Câmp obligatoriu. O adresă de email activă.</span>
              </div>
            </div>

            <div class="group">
              <label class="label">Mesaj</label>
              <textarea class="text_area" name="c[mesaj]" rows="10" cols="80" ><?=$c['mesaj'];?></textarea>
              <span class="description">Câmp obligatoriu. Scurt și la subiect. Ajută-ne pe noi să te ajutăm pe tine!</span>
            </div>
            
            <div class="group">
              <label class="label">Verificare anti-robot</label>
              <?=$recaptcha; ?>
              <span class="description">Câmp obligatoriu. O simplă verificare să știm că ești om.</span>
            </div>
            
            <div class="group navform wat-cf">
              <button class="button" type="submit" name="c[trimite]" >
                <img src="/img/icons/tick.png" alt="Save" /> Trimite
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>