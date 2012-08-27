<?php
namespace Hoopak;


class Trace
{
    /**
     * Create a Trace.
     */
    public function __construct($name, $traceId=null, $spanId=null, $parentSpanId=null, $tracers=array())
    {
        $this->name = $name;

        if ($traceId) {
            $this->traceId = $traceId;
        } else {
            $this->traceId = $this->_id();
        }

        if ($spanId) {
            $this->spanId = $spanId;
        } else {
            $this->spanId = $this->_id();
        }

        $this->parentSpanId = $parentSpanId;

        $this->_tracers = $tracers;

        $this->_endpoint = null;
    }

    /**
     * Record an annotation
     */ 
    public function record(Hoopak\Annotation $annotation)
    {
        if (!$annotation->endpoint && $this->_endpoint) {
            $annotation->endpoint = $this->endpoint;
        }

        foreach ($this->_tracers as $tracer) {
            $tracer->record($annotation);
        }

    }

    /**
     * Create a child of this trace
     */
    public function child($name)
    {
        $trace = new self($name, $this->traceId, null, $this->spanId);
        $trace->setEndpoint($this->_endpoint);
        return $trace;
    }

    /**
     * Set the endpoint
     */
    public function setEndpoint(Hoopak\Endpoint $endpoint)
    {
        $this->_endpoint = $endpoint;
    }

    private static function _id()
    {
        return rand(0, pow(2,63)-1);
    }
    

}
