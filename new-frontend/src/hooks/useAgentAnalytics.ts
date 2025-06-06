'use client';

import { useState, useEffect } from 'react';

export interface LinkClickEvent {
  agentId: string;
  referralCode?: string;
  timestamp: string;
  userAgent: string;
  source: string; // utm_source or 'direct'
  medium: string; // utm_medium or 'direct'
  campaign?: string; // utm_campaign
  ipAddress?: string;
}

export interface AgentLinkAnalytics {
  totalClicks: number;
  uniqueClicks: number;
  clicksBySource: Record<string, number>;
  clicksByMedium: Record<string, number>;
  clicksByCampaign: Record<string, number>;
  clicksByDate: Record<string, number>;
  conversionRate?: number;
}

class AgentAnalyticsService {
  private static STORAGE_KEY = 'agent_link_analytics';

  static trackLinkClick(agentId: string, referralCode?: string): void {
    try {
      // Get URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      
      const event: LinkClickEvent = {
        agentId,
        referralCode,
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent,
        source: urlParams.get('utm_source') || 'direct',
        medium: urlParams.get('utm_medium') || 'direct',
        campaign: urlParams.get('utm_campaign') || undefined,
      };

      // Store in localStorage for demo purposes
      // In production, this should be sent to your analytics API
      const existingData = this.getStoredAnalytics();
      existingData.push(event);
      
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(existingData));
      
      // Send to analytics API (if available)
      this.sendToAnalyticsAPI(event);
      
      console.log('Agent link click tracked:', event);
    } catch (error) {
      console.error('Failed to track link click:', error);
    }
  }

  static getStoredAnalytics(): LinkClickEvent[] {
    try {
      const stored = localStorage.getItem(this.STORAGE_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (error) {
      console.error('Failed to get stored analytics:', error);
      return [];
    }
  }

  static getAgentAnalytics(agentId: string): AgentLinkAnalytics {
    const allEvents = this.getStoredAnalytics();
    const agentEvents = allEvents.filter(event => event.agentId === agentId);

    const analytics: AgentLinkAnalytics = {
      totalClicks: agentEvents.length,
      uniqueClicks: new Set(agentEvents.map(e => e.userAgent + e.timestamp.split('T')[0])).size,
      clicksBySource: {},
      clicksByMedium: {},
      clicksByCampaign: {},
      clicksByDate: {},
    };

    agentEvents.forEach(event => {
      // Count by source
      analytics.clicksBySource[event.source] = (analytics.clicksBySource[event.source] || 0) + 1;
      
      // Count by medium
      analytics.clicksByMedium[event.medium] = (analytics.clicksByMedium[event.medium] || 0) + 1;
      
      // Count by campaign
      if (event.campaign) {
        analytics.clicksByCampaign[event.campaign] = (analytics.clicksByCampaign[event.campaign] || 0) + 1;
      }
      
      // Count by date
      const date = event.timestamp.split('T')[0];
      analytics.clicksByDate[date] = (analytics.clicksByDate[date] || 0) + 1;
    });

    return analytics;
  }

  static getAllAgentsAnalytics(): Record<string, AgentLinkAnalytics> {
    const allEvents = this.getStoredAnalytics();
    const agentIds = [...new Set(allEvents.map(event => event.agentId))];
    
    const result: Record<string, AgentLinkAnalytics> = {};
    agentIds.forEach(agentId => {
      result[agentId] = this.getAgentAnalytics(agentId);
    });
    
    return result;
  }

  private static async sendToAnalyticsAPI(event: LinkClickEvent): Promise<void> {
    // In production, send to your analytics API
    // try {
    //   await fetch('/api/analytics/track-click', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/json' },
    //     body: JSON.stringify(event)
    //   });
    // } catch (error) {
    //   console.error('Failed to send analytics to API:', error);
    // }
  }

  static clearAnalytics(): void {
    localStorage.removeItem(this.STORAGE_KEY);
  }

  static exportAnalytics(): string {
    const data = this.getStoredAnalytics();
    return JSON.stringify(data, null, 2);
  }
}

export function useAgentAnalytics(agentId?: string) {
  const [analytics, setAnalytics] = useState<AgentLinkAnalytics | null>(null);
  const [allAnalytics, setAllAnalytics] = useState<Record<string, AgentLinkAnalytics>>({});
  const [loading, setLoading] = useState(false);

  const trackClick = (trackingAgentId: string, referralCode?: string) => {
    AgentAnalyticsService.trackLinkClick(trackingAgentId, referralCode);
    refreshAnalytics();
  };

  const refreshAnalytics = () => {
    setLoading(true);
    
    if (agentId) {
      const agentAnalytics = AgentAnalyticsService.getAgentAnalytics(agentId);
      setAnalytics(agentAnalytics);
    }
    
    const allAgentsAnalytics = AgentAnalyticsService.getAllAgentsAnalytics();
    setAllAnalytics(allAgentsAnalytics);
    
    setLoading(false);
  };

  const clearAnalytics = () => {
    AgentAnalyticsService.clearAnalytics();
    refreshAnalytics();
  };

  const exportAnalytics = () => {
    return AgentAnalyticsService.exportAnalytics();
  };

  useEffect(() => {
    refreshAnalytics();
  }, [agentId]);

  return {
    analytics,
    allAnalytics,
    loading,
    trackClick,
    refreshAnalytics,
    clearAnalytics,
    exportAnalytics
  };
}