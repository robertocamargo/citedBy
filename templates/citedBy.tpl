{**
 * plugins/generic/citedBy/templates/citedBy.tpl
 *
 * Roberto Camargo @btocamargo
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 * 
 * A template to be included via Templates::Article::Main hook.
 *}
<div class="item citedby" id="citedByList">
{assign var=journalDoi value=$citedby}
{assign var=total value=$total-1}
<h3 class="label">{translate key="plugins.generic.citedBy.heading"}</h3>
<ul style="list-style-type: square;">
{for $num=0 to $total}
    <li>{$journalDoi->query_result->body->forward_link[$num]->journal_cite->doi}</li>
{/for}
</ul>
</div>
