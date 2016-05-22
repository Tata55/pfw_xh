<h1><?=$this->escape($title)?></h1>
<img class="pfw_logo" src="<?=$this->escape($logo)?>" alt="<?=$this->text('alt_logo')?>"/>
<p>Version <?=$this->escape($plugin->version)?></p>
<p>Copyright <?=$this->escape($plugin->copyright)?></p>
<p class="pfw_license">This program is free software: you can redistribute it
and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your
option) any later version.</p>
<p class="pfw_license">This program is distributed in the hope that it will be
useful, but <em>without any warranty</em>; without even the implied warranty of
<em>merchantability</em> or <em>fitness for a particular purpose</em>. See the
GNU General Public License for more details.</p>
<p class="pfw_license">You should have received a copy of the GNU General
Public License along with this program. If not, see <a
href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>
<h4><?=$this->text('syscheck_title')?></h4>
<?=$systemCheck->render()?>
<h4><?=$this->text('userfunc_title')?></h4>
<dl class="pfw_userfuncs">
<?php foreach ($plugin->getFuncNames() as $funcName):?>
    <dt><?=$this->escape($userFuncSignature($funcName))?></dt>
    <dd><?=$this->text("userfunc_$funcName")?></dd>
<?php endforeach?>
</dl>
