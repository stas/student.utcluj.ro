<div id="box">
      <div class="block" id="block-signup">
        <h2>Creare cont student</h2>
        <div class="content">
          <?=flash_h($flash);?>
          <form action="?/creare" method="post" class="form">
            <div class="group wat-cf">
              <div class="left">
                <label class="label">CNP</label>
              </div>
              <div class="right">
                <input type="text" class="text_field" name="s[cnp]" value="<?=$s['cnp'];?>" />
                <span class="description">Câmp obligatoriu. Ex: 1789876543210</span>
              </div>
            </div>
            <div class="group wat-cf">
              <div class="left">
                <label class="label">Cont SINU</label>
              </div>
              <div class="right">
                <input type="text" class="text_field" name="s[cont]" value="<?=$s['cont'];?>" />
                <span class="description">Câmp obligatoriu. Numele de utilizator folosit pe SINU.</span>
              </div>
            </div>
            <div class="group wat-cf">
              <div class="left">
                <label class="label">Parola</label>
              </div>
              <div class="right">
                <input type="password" class="text_field" name="s[parola]" />
                <span class="description">Câmp obligatoriu. Parola folosită pe SINU.</span>
              </div>
            </div>

            <div class="group">
              <label class="label">Alias</label>
              <input type="text" class="text_field" name="s[alias]" value="<?=$s['alias'];?>" />
              <span class="description">Ex: Ion.Popescu pentru a obține adresa Ion.Popescu@student.utcluj.ro</span>
            </div>
            
            <div class="group">
              <label class="label">Verificare anti-robot</label>
              <?=$recaptcha; ?>
              <span class="description">Câmp obligatoriu. O simplă verificare să știm că ești om.</span>
            </div>
            
            <div class="group navform wat-cf">
              <button class="button" type="submit" name="s[trimite]" >
                <img src="/img/icons/tick.png" alt="Save" /> Trimite
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>