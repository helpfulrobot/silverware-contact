<div class="address">
  <% if $Street %>
    <div class="street">
      $Street
    </div>
  <% end_if %>
  <% if $Suburb || $StateTerritory || $PostalCode %>
    <div class="suburb state-territory postal-code">
      <% if $Suburb %><span class="suburb">$Suburb</span><% end_if %>
      <% if $StateTerritory %><span class="state-territory">$StateTerritory</span><% end_if %>
      <% if $PostalCode %><span class="postal-code">$PostalCode</span><% end_if %>
    </div>
  <% end_if %>
  <% if $CountryName %>
    <div class="country">
      $CountryName
    </div>
  <% end_if %>
</div>