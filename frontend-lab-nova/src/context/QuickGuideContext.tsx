import React, { createContext, useContext } from 'react'

interface QuickGuideContextData {
  openGuide: () => void
}

const QuickGuideContext = createContext<QuickGuideContextData | undefined>(undefined)

interface QuickGuideProviderProps {
  children: React.ReactNode
  onOpenGuide: () => void
}

export const QuickGuideProvider: React.FC<QuickGuideProviderProps> = ({ children, onOpenGuide }) => (
  <QuickGuideContext.Provider value={{ openGuide: onOpenGuide }}>
    {children}
  </QuickGuideContext.Provider>
)

export const useQuickGuide = () => {
  const context = useContext(QuickGuideContext)
  if (!context) {
    throw new Error('useQuickGuide must be used within a QuickGuideProvider')
  }
  return context
}
