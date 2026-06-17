import { motion } from 'framer-motion';

interface Props {
    progress: number;
}

export default function LiquidProgressBar({ progress }: Props) {
    return (
        <div className="h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
            <motion.div
                className="h-full bg-primary"
                initial={{ width: 0 }}
                animate={{ 
                    width: `${progress}%`,
                    opacity: [0.8, 1, 0.8]
                }}
                transition={{
                    width: { 
                        type: "spring", 
                        stiffness: 100, 
                        damping: 20
                    },
                    opacity: { 
                        repeat: Infinity, 
                        duration: 3, 
                        ease: "easeInOut" 
                    }
                }}
            />
        </div>
    );
}
