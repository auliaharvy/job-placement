import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { apiGet, apiPost, apiPut, apiDelete, API_ENDPOINTS } from './api';

// Dashboard hooks
export const useDashboard = (dateRange?: string[]) => {
  return useQuery({
    queryKey: ['dashboard', dateRange],
    queryFn: () => apiGet(API_ENDPOINTS.DASHBOARD, { 
      start_date: dateRange?.[0], 
      end_date: dateRange?.[1] 
    }),
    staleTime: 5 * 60 * 1000, // 5 minutes
  });
};

// Applicants hooks
export const useApplicants = (params?: any) => {
  return useQuery({
    queryKey: ['applicants', params],
    queryFn: () => apiGet(API_ENDPOINTS.APPLICANTS, params),
  });
};

export const useApplicant = (id: string) => {
  return useQuery({
    queryKey: ['applicant', id],
    queryFn: () => apiGet(API_ENDPOINTS.APPLICANT_PROFILE(id)),
    enabled: !!id,
  });
};

export const useCreateApplicant = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (data: any) => apiPost(API_ENDPOINTS.APPLICANT_REGISTRATION, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['applicants'] });
    },
  });
};

export const useUpdateApplicant = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: ({ id, data }: { id: string; data: any }) => 
      apiPut(API_ENDPOINTS.APPLICANT_PROFILE(id), data),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['applicant', variables.id] });
      queryClient.invalidateQueries({ queryKey: ['applicants'] });
    },
  });
};

// Jobs hooks
export const useJobs = (params?: any) => {
  return useQuery({
    queryKey: ['jobs', params],
    queryFn: () => apiGet(API_ENDPOINTS.JOBS, params),
  });
};

export const useJob = (id: string) => {
  return useQuery({
    queryKey: ['job', id],
    queryFn: () => apiGet(API_ENDPOINTS.JOB_DETAIL(id)),
    enabled: !!id,
  });
};

export const useCreateJob = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (data: any) => apiPost(API_ENDPOINTS.JOBS, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['jobs'] });
    },
  });
};

export const useUpdateJob = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: ({ id, data }: { id: string; data: any }) => 
      apiPut(API_ENDPOINTS.JOB_DETAIL(id), data),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['job', variables.id] });
      queryClient.invalidateQueries({ queryKey: ['jobs'] });
    },
  });
};

export const useDeleteJob = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (id: string) => apiDelete(API_ENDPOINTS.JOB_DETAIL(id)),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['jobs'] });
    },
  });
};

// Applications hooks
export const useApplications = (params?: any) => {
  return useQuery({
    queryKey: ['applications', params],
    queryFn: () => apiGet(API_ENDPOINTS.APPLICATIONS, params),
  });
};

export const useApplication = (id: string) => {
  return useQuery({
    queryKey: ['application', id],
    queryFn: () => apiGet(API_ENDPOINTS.APPLICATION_DETAIL(id)),
    enabled: !!id,
  });
};

export const useUpdateApplicationStatus = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: ({ id, status, notes }: { id: string; status: string; notes?: string }) => 
      apiPut(API_ENDPOINTS.APPLICATION_UPDATE_STATUS(id), { status, notes }),
    onSuccess: (_, variables) => {
      queryClient.invalidateQueries({ queryKey: ['application', variables.id] });
      queryClient.invalidateQueries({ queryKey: ['applications'] });
      queryClient.invalidateQueries({ queryKey: ['dashboard'] });
    },
  });
};

// Placements hooks
export const usePlacements = (params?: any) => {
  return useQuery({
    queryKey: ['placements', params],
    queryFn: () => apiGet(API_ENDPOINTS.PLACEMENTS, params),
  });
};

export const usePlacement = (id: string) => {
  return useQuery({
    queryKey: ['placement', id],
    queryFn: () => apiGet(API_ENDPOINTS.PLACEMENT_DETAIL(id)),
    enabled: !!id,
  });
};

export const useExpiringPlacements = () => {
  return useQuery({
    queryKey: ['placements', 'expiring'],
    queryFn: () => apiGet(API_ENDPOINTS.PLACEMENT_EXPIRING),
  });
};

// Companies hooks
export const useCompanies = (params?: any) => {
  return useQuery({
    queryKey: ['companies', params],
    queryFn: () => apiGet(API_ENDPOINTS.COMPANIES, params),
  });
};

export const useCompany = (id: string) => {
  return useQuery({
    queryKey: ['company', id],
    queryFn: () => apiGet(API_ENDPOINTS.COMPANY_DETAIL(id)),
    enabled: !!id,
  });
};

export const useCreateCompany = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (data: any) => apiPost(API_ENDPOINTS.COMPANIES, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['companies'] });
    },
  });
};

// Agents hooks
export const useAgents = (params?: any) => {
  return useQuery({
    queryKey: ['agents', params],
    queryFn: () => apiGet(API_ENDPOINTS.AGENTS, params),
  });
};

export const useAgent = (id: string) => {
  return useQuery({
    queryKey: ['agent', id],
    queryFn: () => apiGet(API_ENDPOINTS.AGENT_DETAIL(id)),
    enabled: !!id,
  });
};

export const useAgentLeaderboard = () => {
  return useQuery({
    queryKey: ['agents', 'leaderboard'],
    queryFn: () => apiGet(API_ENDPOINTS.AGENT_LEADERBOARD),
  });
};

// WhatsApp hooks
export const useWhatsAppLogs = (params?: any) => {
  return useQuery({
    queryKey: ['whatsapp', 'logs', params],
    queryFn: () => apiGet(API_ENDPOINTS.WHATSAPP_LOGS, params),
  });
};

export const useSendWhatsApp = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (data: any) => apiPost(API_ENDPOINTS.WHATSAPP_SEND, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['whatsapp', 'logs'] });
    },
  });
};

export const useBroadcastWhatsApp = () => {
  const queryClient = useQueryClient();
  
  return useMutation({
    mutationFn: (data: any) => apiPost(API_ENDPOINTS.WHATSAPP_BROADCAST, data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['whatsapp', 'logs'] });
    },
  });
};

// Analytics hooks
export const useAnalytics = (params?: any) => {
  return useQuery({
    queryKey: ['analytics', params],
    queryFn: () => apiGet(API_ENDPOINTS.ANALYTICS, params),
  });
};

export const useAnalyticsReports = (params?: any) => {
  return useQuery({
    queryKey: ['analytics', 'reports', params],
    queryFn: () => apiGet(API_ENDPOINTS.ANALYTICS_REPORTS, params),
  });
};
