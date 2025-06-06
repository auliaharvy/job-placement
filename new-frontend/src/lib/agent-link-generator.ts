export interface AgentLinkConfig {
  agentId: string;
  baseUrl?: string;
  utmSource?: string;
  utmMedium?: string;
  utmCampaign?: string;
}

export class AgentLinkGenerator {
  /**
   * Generate a link with agent ID parameter
   */
  static generateAgentLink(config: AgentLinkConfig): string {
    const { agentId, baseUrl, utmSource, utmMedium, utmCampaign } = config;
    
    // Use current page URL if baseUrl is not provided
    const currentUrl = baseUrl || (typeof window !== 'undefined' ? window.location.href.split('?')[0] : '');
    const url = new URL(currentUrl);
    
    // Add agent parameter
    url.searchParams.set('agent', agentId);
    
    // Add UTM parameters if provided
    if (utmSource) url.searchParams.set('utm_source', utmSource);
    if (utmMedium) url.searchParams.set('utm_medium', utmMedium);
    if (utmCampaign) url.searchParams.set('utm_campaign', utmCampaign);
    
    return url.toString();
  }

  /**
   * Generate a link with referral code parameter
   */
  static generateReferralLink(referralCode: string, baseUrl?: string): string {
    const currentUrl = baseUrl || (typeof window !== 'undefined' ? window.location.href.split('?')[0] : '');
    const url = new URL(currentUrl);
    
    url.searchParams.set('ref', referralCode);
    
    return url.toString();
  }

  /**
   * Generate QR code URL for agent link
   */
  static generateQRCodeUrl(agentLink: string, size: number = 300): string {
    const encodedUrl = encodeURIComponent(agentLink);
    return `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodedUrl}`;
  }

  /**
   * Extract agent ID from URL parameters
   */
  static extractAgentFromUrl(url: string): { agentId?: string; referralCode?: string } {
    try {
      const urlObj = new URL(url);
      const agentId = urlObj.searchParams.get('agent');
      const referralCode = urlObj.searchParams.get('ref');
      
      return {
        agentId: agentId || undefined,
        referralCode: referralCode || undefined
      };
    } catch (error) {
      console.error('Invalid URL:', error);
      return {};
    }
  }

  /**
   * Generate multiple link variants for an agent
   */
  static generateAgentLinkVariants(agentId: string, referralCode: string, baseUrl?: string) {
    const currentUrl = baseUrl || (typeof window !== 'undefined' ? window.location.href.split('?')[0] : '');
    
    return {
      agentLink: this.generateAgentLink({ agentId, baseUrl: currentUrl }),
      referralLink: this.generateReferralLink(referralCode, currentUrl),
      socialMediaLink: this.generateAgentLink({
        agentId,
        baseUrl: currentUrl,
        utmSource: 'social',
        utmMedium: 'referral',
        utmCampaign: `agent_${agentId}`
      }),
      emailLink: this.generateAgentLink({
        agentId,
        baseUrl: currentUrl,
        utmSource: 'email',
        utmMedium: 'referral',
        utmCampaign: `agent_${agentId}`
      }),
      whatsappLink: this.generateAgentLink({
        agentId,
        baseUrl: currentUrl,
        utmSource: 'whatsapp',
        utmMedium: 'referral',
        utmCampaign: `agent_${agentId}`
      })
    };
  }

  /**
   * Generate WhatsApp share link
   */
  static generateWhatsAppShareLink(agentLink: string, message?: string): string {
    const defaultMessage = `Halo! Saya ingin membantu Anda dalam pencarian kerja. Silakan klik link berikut untuk memulai: ${agentLink}`;
    const text = encodeURIComponent(message || defaultMessage);
    return `https://wa.me/?text=${text}`;
  }

  /**
   * Generate Telegram share link
   */
  static generateTelegramShareLink(agentLink: string, message?: string): string {
    const defaultMessage = `Halo! Saya ingin membantu Anda dalam pencarian kerja. Silakan klik link berikut: ${agentLink}`;
    const text = encodeURIComponent(message || defaultMessage);
    return `https://t.me/share/url?url=${encodeURIComponent(agentLink)}&text=${text}`;
  }

  /**
   * Generate email share link
   */
  static generateEmailShareLink(agentLink: string, subject?: string, body?: string): string {
    const defaultSubject = 'Kesempatan Kerja Terbaik Untuk Anda';
    const defaultBody = `Halo!\n\nSaya ingin membantu Anda mendapatkan pekerjaan impian. Silakan klik link berikut untuk memulai proses aplikasi:\n\n${agentLink}\n\nSaya siap membantu Anda dalam setiap langkah proses aplikasi.\n\nTerima kasih!`;
    
    const mailtoSubject = encodeURIComponent(subject || defaultSubject);
    const mailtoBody = encodeURIComponent(body || defaultBody);
    
    return `mailto:?subject=${mailtoSubject}&body=${mailtoBody}`;
  }
}