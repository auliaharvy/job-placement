'use client';

import { useState, useEffect } from 'react';
import { useSearchParams } from 'next/navigation';
import { Agent, AgentService } from '@/lib/agent';
import { useAgentAnalytics } from './useAgentAnalytics';

interface UseAgentAutoFillReturn {
  selectedAgentId: string;
  selectedAgent: Agent | null;
  loading: boolean;
  error: string | null;
  setSelectedAgent: (agentId: string, agent: Agent | null) => void;
  generateAgentLink: (agentId: string, baseUrl?: string) => string;
}

export function useAgentAutoFill(): UseAgentAutoFillReturn {
  const searchParams = useSearchParams();
  const [selectedAgentId, setSelectedAgentId] = useState<string>('');
  const [selectedAgent, setSelectedAgent] = useState<Agent | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const { trackClick } = useAgentAnalytics();

  useEffect(() => {
    const agentParam = searchParams.get('agent');
    const referralParam = searchParams.get('ref');
    
    if (agentParam) {
      // If agent ID is provided in URL
      loadAgentById(agentParam);
    } else if (referralParam) {
      // If referral code is provided in URL
      loadAgentByReferralCode(referralParam);
    }
  }, [searchParams]);

  const loadAgentById = async (agentId: string) => {
    try {
      setLoading(true);
      setError(null);
      
      // Get all agents and find the one with matching ID
      const agents = await AgentService.getAllAgents();
      const agent = agents.find(a => a.id.toString() === agentId);
      
      if (agent) {
        setSelectedAgentId(agentId);
        setSelectedAgent(agent);
        // Track the link click
        trackClick(agentId, agent.referral_code);
      } else {
        setError(`Agent with ID ${agentId} not found`);
      }
    } catch (err: any) {
      setError(err.message);
      console.error('Error loading agent by ID:', err);
    } finally {
      setLoading(false);
    }
  };

  const loadAgentByReferralCode = async (referralCode: string) => {
    try {
      setLoading(true);
      setError(null);
      
      const agent = await AgentService.getAgentByReferralCode(referralCode);
      
      if (agent) {
        setSelectedAgentId(agent.id.toString());
        setSelectedAgent(agent);
        // Track the link click
        trackClick(agent.id.toString(), agent.referral_code);
      } else {
        setError(`Agent with referral code ${referralCode} not found`);
      }
    } catch (err: any) {
      setError(err.message);
      console.error('Error loading agent by referral code:', err);
    } finally {
      setLoading(false);
    }
  };

  const setSelectedAgentHandler = (agentId: string, agent: Agent | null) => {
    setSelectedAgentId(agentId);
    setSelectedAgent(agent);
  };

  const generateAgentLink = (agentId: string, baseUrl?: string): string => {
    const currentUrl = baseUrl || window.location.origin + window.location.pathname;
    const url = new URL(currentUrl);
    url.searchParams.set('agent', agentId);
    return url.toString();
  };

  return {
    selectedAgentId,
    selectedAgent,
    loading,
    error,
    setSelectedAgent: setSelectedAgentHandler,
    generateAgentLink
  };
}