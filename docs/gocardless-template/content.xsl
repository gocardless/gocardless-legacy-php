<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />

  <xsl:template match="/project">
    <div class="content">
      <h2>GoCardless PHP Client Library</h2>

      <p>The GoCardless PHP client provides a simple PHP interface to the GoCardless
      API.</p>

      <p>If you want to use the library as an individual merchant, refer to the
      <a href="https://gocardless.com/docs/php/merchant_client_guide">merchant guide</a>. If
      you want to support multiple merchant accounts, see the
      <a href="https://gocardless.com/docs/php/partner_client_guide">partner guide</a>.</p>
    </div>
  </xsl:template>

</xsl:stylesheet>
